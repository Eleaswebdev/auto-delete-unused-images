jQuery(document).ready(function ($) {
    $('#scan-unused-images').on('click', function () {
        var button = $(this);
        button.text('Scanning...').prop('disabled', true);
    
        $.post(unusedImagesCleaner.ajaxurl, {
            action: 'scan_unused_images',
            security: unusedImagesCleaner.nonce
        }, function (response) {
            button.text('Scan Now').prop('disabled', false);
            var resultDiv = $('#results').html('');
    
            if (response.success && response.data.length > 0) {
                var tableHtml = '<table id="unused-images-table" class="display nowrap" style="width:100%">';
                tableHtml += '<thead><tr><th><input type="checkbox" id="select-all"></th><th>Image</th><th>File Name</th><th>Action</th></tr></thead><tbody>';
    
                response.data.forEach(function (image) {
                    var filename = image.url.split('/').pop();
                    tableHtml += '<tr>' +
                        '<td><input type="checkbox" class="image-checkbox" data-id="' + image.id + '"></td>' +
                        '<td><img src="' + image.url + '" width="80"></td>' +
                        '<td>' + filename + '</td>' +
                        '<td><button class="delete-image button button-danger" data-id="' + image.id + '">Delete</button></td>' +
                        '</tr>';
                });
    
                tableHtml += '</tbody></table>';
                resultDiv.html(tableHtml);
    
                // Initialize DataTables
                $('#unused-images-table').DataTable({
                    responsive: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    pageLength: 5,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    columnDefs: [{ orderable: false, targets: [0, 1, 3] }] // Disable sorting for checkbox, image, and action columns
                });
    
                $('#delete-selected-images').show();
            } else {
                resultDiv.html('<p>No unused images found.</p>');
                $('#delete-selected-images').hide();
            }
        });
    });

    $(document).on('click', '.delete-image', function () {
        
        var button = $(this);
        var imageID = button.data('id');

        if (!confirm("Do you want to delete this image? Remember, this action is irreversible and it will delete the image from the media. Click OK to proceed.")) {
            return; 
        }
    
        $.post(unusedImagesCleaner.ajaxurl, {
            action: 'delete_unused_images',
            security: unusedImagesCleaner.nonce,
            image_ids: [imageID]
        }, function (response) {
            if (response.success) {
                var row = button.closest('tr'); // Find the nearest row
                var table = $('#unused-images-table').DataTable(); // Get DataTable instance
                table.row(row).remove().draw(); // Remove row from DataTable and redraw
            }
        });
    });

    $(document).on('change', '#select-all', function () {
        $('.image-checkbox').prop('checked', this.checked);
    });

    $('#delete-selected-images').on('click', function () {
        if (!confirm("Do you want to delete this image? Remember, this action is irreversible and it will delete the image from the media. Click OK to proceed.")) {
            return; 
        }
        var selectedImages = $('.image-checkbox:checked').map(function () {
            return $(this).data('id');
        }).get();
    
        if (selectedImages.length === 0) {
            alert('No images selected.');
            return;
        }
    
        $.post(unusedImagesCleaner.ajaxurl, {
            action: 'delete_unused_images',
            security: unusedImagesCleaner.nonce,
            image_ids: selectedImages
        }, function (response) {
            if (response.success) {
                var table = $('#unused-images-table').DataTable(); // Get DataTable instance
                $('.image-checkbox:checked').each(function () {
                    var row = $(this).closest('tr'); // Find the full row
                    table.row(row).remove().draw(); // Remove row from DataTable
                });
    
                $('#delete-selected-images').hide();
            }
        });
    });
    
});
