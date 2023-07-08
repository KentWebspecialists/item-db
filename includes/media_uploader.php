<?php

function itemdb_add_image_box() {
    add_meta_box(
        'itemdb_image_box_id',              // Unique ID
        'Image Uploads',                     // Box title
        'itemdb_image_box_html',             // Content callback
        'db',                                // Post type
        'normal',                            // Context
        'high'                               // Priority
    );
}
add_action('add_meta_boxes', 'itemdb_add_image_box');

function itemdb_image_box_html($post) {
    wp_nonce_field(plugin_basename(__FILE__), 'itemdb_images_nonce');
    $image_meta = get_post_meta($post->ID, 'itemdb_images', true);
    $images = $image_meta ? explode(",", $image_meta) : [];
    echo '<div class="itemdb-gallery-container">';
    foreach($images as $image) {
        echo '<div class="itemdb-image"><img src="' . $image . '" style="max-width: 200px; max-height: 200px;"><button class="itemdb-remove-image-button" type="button">Remove</button></div>';
    }
    echo '</div>';
    echo '<input id="itemdb-image-field" type="hidden" name="itemdb_images_field" value="' . $image_meta . '" />';
    echo '<button type="button" id="itemdb-upload-button" class="button">Upload Images</button>';
}

function itemdb_save_image_data($post_id) {
    // Verify nonce
    if (!isset($_POST['itemdb_images_nonce']) || !wp_verify_nonce($_POST['itemdb_images_nonce'], plugin_basename(__FILE__))) {
        return $post_id;
    }
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if (array_key_exists('itemdb_images_field', $_POST)) {
        update_post_meta(
            $post_id,
            'itemdb_images',
            $_POST['itemdb_images_field']
        );
    }
}
add_action('save_post', 'itemdb_save_image_data');
