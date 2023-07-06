<?php

function itemdb_add_custom_box() {
    add_meta_box(
        'itemdb_box_id',               // Unique ID
        'Item Main Description',                // Box title
        'itemdb_custom_box_html',      // Content callback
        'db',                          // Post type
        'normal',                      // Context
        'high'                         // Priority
    );
}
add_action('add_meta_boxes', 'itemdb_add_custom_box');

function itemdb_custom_box_html($post) {
    $content = get_post_meta($post->ID, 'itemdb_main_text', true);
    wp_editor($content, 'itemdb_main_text_field', $settings = array('textarea_name'=>'itemdb_main_text_field'));
}