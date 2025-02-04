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
        $image_id = $image->ID;
        $image_url = wp_get_attachment_url($image_id);
    
        // Check Block Editor, Post, Page, Excerpt, Featured Image
        $is_used_in_posts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM {$wpdb->posts} 
             WHERE post_type NOT IN ('revision') 
             AND (post_content LIKE %s OR post_excerpt LIKE %s)
             OR ID IN (
                 SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value = %d
             )
             OR ID IN (
                 SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_thumbnail_id' AND meta_value = %d
             )",
            '%' . $wpdb->esc_like('"id":' . $image_id) . '%',
            '%' . $wpdb->esc_like('"id":' . $image_id) . '%',
            $image_id,
            $image_id
        ));
    
        // Check Elementor Metadata
        $escaped_image_url = str_replace('/', '\/', $image_url);
        $is_used_in_elementor = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE p.post_status = 'publish' 
             AND pm.meta_key = '_elementor_data' 
             AND (pm.meta_value LIKE %s OR pm.meta_value REGEXP %s)",
            '%"id":"' . $image_id . '"%',
            '"url":"[^"]*' . $wpdb->esc_like($escaped_image_url) . '[^"]*"'
        ));
    
        // Determine final usage status
        $is_used = ($is_used_in_posts > 0 || $is_used_in_elementor > 0) ? 1 : 0;

    
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
