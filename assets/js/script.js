jQuery(document).ready(function ($) {
    $('#scan-unused-images').on('click', function () {
        var button = $(this);
        button.text('Scanning...').prop('disabled', true);

        $.post(unusedImagesCleaner.ajaxurl, {
            action: 'scan_unused_images',
            security: unusedImagesCleaner.nonce
        }, function (response) {
            button.text('Scan Now').prop('disabled', false);
            if (response.success) {
                var resultDiv = $('#results').html('<h3>Unused Images</h3><input type="checkbox" id="select-all"> Select All');
                var deleteButton = $('#delete-selected-images').hide();
                response.data.forEach(function (image) {
                    resultDiv.append(
                        '<div class="image-item">' +
                        '<input type="checkbox" class="image-checkbox" data-id="' + image.id + '">' +
                        '<img src="' + image.url + '" width="100"> ' +
                        '<button class="delete-image button" data-id="' + image.id + '">Delete</button></div>'
                    );
                });
                if (response.data.length > 0) {
                    deleteButton.show();
                }
            }
        });
    });

    $(document).on('click', '.delete-image', function () {
        var button = $(this);
        var imageID = button.data('id');

        $.post(unusedImagesCleaner.ajaxurl, {
            action: 'delete_unused_images',
            security: unusedImagesCleaner.nonce,
            image_ids: [imageID]
        }, function (response) {
            if (response.success) {
                button.parent().remove();
            }
        });
    });

    $(document).on('change', '#select-all', function () {
        $('.image-checkbox').prop('checked', this.checked);
    });

    $('#delete-selected-images').on('click', function () {
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
                $('.image-checkbox:checked').closest('.image-item').remove();
                $('#delete-selected-images').hide();
            }
        });
    });
});
