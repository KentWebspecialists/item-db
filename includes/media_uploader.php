<?php

function itemdb_add_image_box() {
    add_meta_box(
        'itemdb_image_box_id',             // Unique ID
        'Image Uploads',                    // Box title
        'itemdb_image_box_html',           // Content callback
        'db',                              // Post type
        'normal',                          // Context
        'high'                             // Priority
    );
}
add_action('add_meta_boxes', 'itemdb_add_image_box');

function itemdb_image_box_html($post) {
    // Image fields
    $image_meta = get_post_meta($post->ID, 'itemdb_images', true);
    $images = $image_meta ? explode(",", $image_meta) : [];

    echo '<div class="itemdb-gallery-container">';
    foreach($images as $image) {
        echo '<div class="itemdb-image"><img src="' . $image . '" style="max-width: 200px; max-height: 200px;"></div>';
    }
    echo '</div>';

    echo '<input id="itemdb-image-field" type="hidden" name="itemdb_images_field" value="' . $image_meta . '" />';
    echo '<button type="button" id="itemdb-upload-button" class="button">Upload Images</button>';
}


function itemdb_save_image_data($post_id) {
    if (array_key_exists('itemdb_images_field', $_POST)) {
        update_post_meta(
            $post_id,
            'itemdb_images',
            $_POST['itemdb_images_field']
        );
    }
}
add_action('save_post', 'itemdb_save_image_data');