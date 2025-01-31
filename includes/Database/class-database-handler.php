<?php

class Auto_Delete_Unused_Images_Database_Handler {
    // Get unused images from the database
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
    
        // Check if the image is used in the current content, excerpt, custom fields, or as a featured image
        $is_used = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM {$wpdb->posts} 
             WHERE (post_content LIKE %s OR post_excerpt LIKE %s) 
             OR ID IN (
                 SELECT post_id 
                 FROM {$wpdb->postmeta} 
                 WHERE meta_value LIKE %s
             )
             OR ID IN (
                 SELECT post_id 
                 FROM {$wpdb->postmeta} 
                 WHERE meta_key = '_thumbnail_id' AND meta_value = %d
             )",
            '%' . $wpdb->esc_like($image->guid) . '%',  // Post content check
            '%' . $wpdb->esc_like($image->guid) . '%',  // Excerpt check
            '%' . $wpdb->esc_like($image->guid) . '%',  // Custom field check
            $image->ID                                 // Featured image check
        ));
    
        // If the image is not used in the current content, check if it is used in Elementor's metadata
        if ($is_used == 0) {
            $is_used_in_elementor = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$wpdb->postmeta} 
                 WHERE (meta_key = '_elementor_data' AND meta_value LIKE %s)
                 OR (meta_key = '_elementor_css' AND meta_value LIKE %s)
                 OR (meta_key = '_elementor_page_settings' AND meta_value LIKE %s)",
                '%' . $wpdb->esc_like($image->guid) . '%',  // Elementor data check
                '%' . $wpdb->esc_like($image->guid) . '%',  // Elementor CSS check
                '%' . $wpdb->esc_like($image->guid) . '%'   // Elementor page settings check
            ));
    
            if ($is_used_in_elementor > 0) {
                $is_used = 1;
            }
        }
    
        // If the image is still not used, check if it was used in revisions
        if ($is_used == 0) {
            $is_used_in_revisions = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$wpdb->posts} 
                 WHERE post_type = 'revision' 
                 AND (post_content LIKE %s OR post_excerpt LIKE %s)",
                '%' . $wpdb->esc_like($image->guid) . '%',  // Revision content check
                '%' . $wpdb->esc_like($image->guid) . '%'   // Revision excerpt check
            ));
    
            // If the image is used in revisions, mark it as used
            if ($is_used_in_revisions > 0) {
                $is_used = 1;
            }
        }
    
        return $is_used;
    }
    

    // Delete images from the database
    public function delete_images($image_ids) {
        foreach ($image_ids as $image_id) {
            wp_delete_attachment($image_id, true);
        }
    }
}
