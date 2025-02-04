<?php

class ADUI_Database_Handler {
   
    // Scan for unused images
    public function get_unused_images() {
        global $wpdb;

        // Query to get all images in the media library
        $query = "SELECT ID, guid FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'";
        $results = $wpdb->get_results($query);

        $unused_images = array();
        foreach ($results as $image) {
            $is_used = $this->check_image_usage($image);
            if ($is_used == 0) {
                $unused_images[] = array(
                    'id'   => $image->ID,
                    'url'  => wp_get_attachment_url($image->ID)
                );
            }
        }
        return $unused_images;
    }

    // Check if the image is used anywhere
    private function check_image_usage($image) {
        global $wpdb;
    
        // Check if image is used in any published post (excluding revisions)
        $is_used = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM {$wpdb->posts} 
             WHERE post_type NOT IN ('revision') 
             AND (post_content LIKE %s OR post_excerpt LIKE %s)
             OR ID IN (
                 SELECT post_id 
                 FROM {$wpdb->postmeta} 
                 WHERE meta_value = %d
             )
             OR ID IN (
                 SELECT post_id 
                 FROM {$wpdb->postmeta} 
                 WHERE meta_key = '_thumbnail_id' AND meta_value = %d
             )",
            '%' . $wpdb->esc_like('"id":' . $image->ID) . '%',  // Matches Gutenberg image ID format
            '%' . $wpdb->esc_like('"id":' . $image->ID) . '%',  
            $image->ID,
            $image->ID
        ));


        // Check Elementor metadata (if Elementor is in use)
        if ($is_used == 0) {
            // Initialize the variable before using it
            $is_used_in_elementor = 0; // Set default value
            
            // Query Elementor data for the image reference
            $is_used_in_elementor = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$wpdb->postmeta} 
                 WHERE (meta_key = '_elementor_data' AND meta_value LIKE %s)
                 OR (meta_key = '_elementor_css' AND meta_value LIKE %s)
                 OR (meta_key = '_elementor_page_settings' AND meta_value LIKE %s)",
                '%' . $wpdb->esc_like($image->guid) . '%',  // Check against the image GUID
                '%' . $wpdb->esc_like($image->guid) . '%',  // Check in Elementor's CSS metadata
                '%' . $wpdb->esc_like($image->guid) . '%'   // Check in Elementor's page settings
            ));
        
            // Also check for the image URL within the serialized JSON in Elementor's postmeta
            if ($is_used_in_elementor == 0) {
                $image_url = wp_get_attachment_url($image->ID);  // Get the URL of the image
                
                // Query Elementor data for the image URL inside the widget settings
                $is_used_in_elementor = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) 
                     FROM {$wpdb->postmeta} 
                     WHERE (meta_key = '_elementor_data' AND meta_value LIKE %s)",
                    '%' . $wpdb->esc_like($image_url) . '%'
                ));
            }
            // Log the result for debugging
            error_log('Elementor usage check: ' . print_r($is_used_in_elementor, true));
            
            // If the image is used in Elementor, mark it as used
            if ($is_used_in_elementor > 0) {
                $is_used = 1;
            }
        }
        
        

      

        

        
    
        return $is_used;
    }
    
    
    
    

    // Delete selected images
    public function delete_unused_images($image_ids) {
        check_ajax_referer('unused_images_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access.');
        }

        $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();

        foreach ($image_ids as $image_id) {
            wp_delete_attachment($image_id, true);
        }

        wp_send_json_success('Selected images deleted successfully.');
    }
}
