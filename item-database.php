<?php
/*
Plugin Name: Item Database
Plugin URI: https://www.kentwebspecialists.co.uk/
Description: Add items to a grid like table via shortcode entered into the item database page with custom optional fields
Version: 1.0.0
Author: Bradly Spicer
Author URI: https://www.bradlyspicer.co.uk
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: item-db
Domain Path: /languages
*/
require_once plugin_dir_path(__FILE__) . 'includes/itemdb-shortcode.php';

function itemdb_post_type() {
    register_post_type( 'item-db',
        array(
            'labels' => array(
                'name' => __( 'Items' ),
                'singular_name' => __( 'Item DB' )
            ),
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail', 'custom-fields'),
        'has_archive' => true,
        'rewrite'   => array( 'slug' => 'item-db' ),
            'menu_position' => 5,
        'menu_icon' => 'dashicons-layout',
        )
    );
}
add_action( 'init', 'itemdb_post_type' );

//// Add item-db taxonomy
function create_items_taxonomy() {
    register_taxonomy('item-db','recipes',array(
        'hierarchical' => false,
        'labels' => array(
            'name' => _x( 'Items', 'taxonomy general name' ),
            'singular_name' => _x( 'Items', 'taxonomy singular name' ),
            'menu_name' => __( 'Item DB' ),
            'all_items' => __( 'All Items' ),
            'edit_item' => __( 'Edit Items' ), 
            'update_item' => __( 'Update Items' ),
            'add_new_item' => __( 'Add Items' ),
            'new_item_name' => __( 'New Items' ),
        ),
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    ));
    register_taxonomy('fields','recipes',array(
        'hierarchical' => false,
        'labels' => array(
            'name' => _x( 'fields', 'taxonomy general name' ),
            'singular_name' => _x( 'Fields', 'taxonomy singular name' ),
            'menu_name' => __( 'Fields' ),
            'all_items' => __( 'All Fields' ),
            'edit_item' => __( 'Edit Fields' ), 
            'update_item' => __( 'Update Fields' ),
            'add_new_item' => __( 'Add Fields' ),
            'new_item_name' => __( 'New Fields' ),
        ),
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    ));
}
add_action( 'init', 'create_items_taxonomy', 0 );

function itemdb_create_categories() {
    $labels = array(
        'name' => _x('Item Categories', 'taxonomy general name'),
        'singular_name' => _x('Item Category', 'taxonomy singular name'),
        'search_items' => __('Search Item Categories'),
        'all_items' => __('All Item Categories'),
        'parent_item' => __('Parent Item Category'),
        'parent_item_colon' => __('Parent Item Category:'),
        'edit_item' => __('Edit Item Category'),
        'update_item' => __('Update Item Category'),
        'add_new_item' => __('Add New Item Category'),
        'new_item_name' => __('New Item Category Name'),
        'menu_name' => __('Item Categories'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'item-category'),
    );

    register_taxonomy('item_category', array('item-db'), $args);
}
add_action('init', 'itemdb_create_categories');

// function itemdb_add_meta_boxes() {
//     add_meta_box('itemdb_custom_fields', 'Item Data', 'itemdb_custom_fields_callback', 'item-db');
// }
// add_action('add_meta_boxes', 'itemdb_add_meta_boxes');

function itemdb_enqueue_frontend_scripts() {
    wp_enqueue_style('itemdb-style', plugin_dir_url(__FILE__) . 'includes/styles.css', array(), '1.0.0');
    wp_enqueue_script('itemdb-script', plugin_dir_url(__FILE__) . 'includes/itemdb.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'itemdb_enqueue_frontend_scripts');

function itemdb_enqueue_admin_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    wp_enqueue_script('itemdb-admin-script', plugin_dir_url(__FILE__) . 'includes/admin-script.js', array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'itemdb_enqueue_admin_scripts');

function itemdb_custom_fields_callback($post) {
    wp_nonce_field('itemdb_save_custom_fields', 'itemdb_custom_fields_nonce');

    $custom_fields = get_post_meta($post->ID, '_itemdb_custom_fields', true);

    echo '<div id="itemdb-custom-fields-container">';
    if (!empty($custom_fields)) {
        foreach ($custom_fields as $field) {
            echo '<div class="itemdb-custom-field">';
            echo '<input type="text" name="itemdb_custom_fields[][label]" placeholder="Label" value="' . esc_attr($field['label']) . '">';
            echo '<input type="text" name="itemdb_custom_fields[][value]" placeholder="Value" value="' . esc_attr($field['value']) . '">';
            echo '<a href="#" class="itemdb-remove-field button">Remove</a>';
            echo '</div>';
        }
    }
    echo '</div>';
    echo '<p><a href="#" id="itemdb-add-field" class="button">Add Field</a></p>';

    // Add a template for new custom fields
    echo '<div id="itemdb-custom-field-template" style="display:none;">';
    echo '<div class="itemdb-custom-field">';
    echo '<input type="text" name="itemdb_custom_fields[][label]" placeholder="Label">';
    echo '<input type="text" name="itemdb_custom_fields[][value]" placeholder="Value">';
    echo '<a href="#" class="itemdb-remove-field button">Remove</a>';
    echo '</div>';
    echo '</div>';
}


function itemdb_save_custom_fields($post_id) {
    if (!isset($_POST['itemdb_custom_fields_nonce']) || !wp_verify_nonce($_POST['itemdb_custom_fields_nonce'], 'itemdb_save_custom_fields')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $custom_fields = isset($_POST['itemdb_custom_fields']) ? (array) $_POST['itemdb_custom_fields'] : array();
    $sanitized_fields = array();

    foreach ($custom_fields as $field) {
        $sanitized_fields[] = array(
            'label' => sanitize_text_field($field['label']),
            'value' => sanitize_text_field($field['value']),
        );
    }

    update_post_meta($post_id, '_itemdb_custom_fields', $sanitized_fields);
}

add_action('save_post', 'itemdb_save_custom_fields');

add_shortcode('ItemDB', 'itemdb_display_items');

// //////////////// //
// //////////////// //
// //////////////// //
// Settings Section //
// //////////////// //
// //////////////// //
// //////////////// //

function itemdb_register_settings() {
    register_setting('itemdb_options', 'itemdb_google_api_key');
}

function itemdb_add_settings_menu() {
    add_options_page('ItemDB Settings', 'ItemDB Settings', 'manage_options', 'itemdb-settings', 'itemdb_settings_page');

    add_settings_section(
        'itemdb_google_sheets_section',
        __( 'Google Sheets Integration', 'itemdb' ),
        'itemdb_google_sheets_settings_section',
        'itemdb_settings'
    );
    

}

add_action('admin_init', 'itemdb_register_settings');
add_action('admin_menu', 'itemdb_add_settings_menu');



// Google Sheets Integration
function itemdb_google_sheets_settings_section() {
    echo '<p>' . __( 'Connect to Google Sheets to import data into the item database.', 'itemdb' ) . '</p>';

    // Add the API Key field
    echo '<label for="itemdb_google_api_key">' . __( 'Google API Key', 'itemdb' ) . '</label><br>';
    echo '<input type="text" id="itemdb_google_api_key" name="itemdb_google_api_key" value="' . esc_attr(get_option('itemdb_google_api_key')) . '" />';
}

// Include the settings page file
require_once( plugin_dir_path( __FILE__ ) . 'includes/settings-page.php' );