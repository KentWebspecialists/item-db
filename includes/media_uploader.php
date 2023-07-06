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
        echo '<img src="' . $image . '" style="max-width: 200px; max-height: 200px;">';
    }
    echo '</div>';

    echo '<input id="itemdb-image-field" type="hidden" name="itemdb_images_field" value="' . $image_meta . '" />';
    echo '
        <div id="itemdb-upload-button" class="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
          </svg>
        </div>
    ';
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