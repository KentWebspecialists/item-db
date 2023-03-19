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

 // //////////////// //
 // //////////////// //
 // //////////////// //
// Post Type Register //
 // //////////////// //
 // //////////////// //
 // //////////////// //

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
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
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

// //////////////// //
// //////////////// //
// //////////////// //
// Enqueue Scripts  //
// //////////////// //
// //////////////// //
// //////////////// //

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


// //////////////// //
// //////////////// //
// //////////////// //
//  Custom Fields   //
// //////////////// //
// //////////////// //
// //////////////// //

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



// //////////////// //
// //////////////// //
// //////////////// //
// Export Items CSV //
// //////////////// //
// //////////////// //
// //////////////// //


// Add custom bulk action to the edit.php screen
add_filter( 'bulk_actions-edit-item-db', 'custom_export_bulk_action' );
function custom_export_bulk_action( $bulk_actions ) {
    $bulk_actions['export_to_csv'] = 'Export to CSV';
    return $bulk_actions;
}

// Process the custom bulk action
add_action( 'admin_action_export_to_csv', 'custom_export_bulk_action_handler' );
function custom_export_bulk_action_handler() {
    // Get selected post IDs
    $post_ids = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : array();

    // Get post data
    $posts = get_posts( array(
        'post_type' => 'item-db',
        'post__in' => $post_ids,
        'posts_per_page' => -1,
    ) );

    // Output CSV file
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="custom-export.csv"' );
    $output = fopen( 'php://output', 'w' );
    fputcsv( $output, array( 'Title', 'Content', 'Custom Fields' ) );
    foreach ( $posts as $post ) {
        $custom_fields = get_post_meta( $post->ID );
        $custom_fields_array = array();
        foreach ( $custom_fields as $key => $value ) {
            // Exclude some meta keys from export (change or remove as needed)
            if ( ! in_array( $key, array( '_edit_lock', '_edit_last' ) ) ) {
                $custom_fields_array[] = $key . ': ' . implode( ', ', $value );
            }
        }
        fputcsv( $output, array( $post->post_title, $post->post_content, implode( '; ', $custom_fields_array ) ) );
    }    
    fclose( $output );
    exit;
}

// //////////////// //
// //////////////// //
// //////////////// //
// Import Items CSV //
// //////////////// //
// //////////////// //
// //////////////// //


// CSV Import Settings Section Callback
function itemdb_csv_settings_section_callback() {
    echo '<p>' . __( 'Upload a CSV file to import item data.', 'itemdb' ) . '</p>';
}

// CSV Import Field Callback
function itemdb_csv_import_field_callback() {
    echo '<input type="file" name="itemdb_csv_import_file">';
}

// CSV Import Button Callback
function itemdb_csv_import_button_callback() {
    echo '<button class="button" type="submit" name="itemdb_csv_import" value="import">' . __( 'Import', 'itemdb' ) . '</button>';
}

// Process CSV Import
add_action( 'admin_post_itemdb_csv_import', 'itemdb_csv_import' );

function itemdb_csv_import() {
    // Check if the file was uploaded
    if ( isset( $_FILES['itemdb_csv_file'] ) && ! empty( $_FILES['itemdb_csv_file']['tmp_name'] ) ) {
        // Get the file
        $file = $_FILES['itemdb_csv_file']['tmp_name'];
        $handle = fopen( $file, 'r' );

        // Process the file line by line
        while ( $data = fgetcsv( $handle ) ) {
            $post_title = isset( $data[0] ) ? $data[0] : '';
            $post_content = isset( $data[1] ) ? $data[1] : '';
            $post_custom_fields = isset( $data[2] ) ? explode( '; ', $data[2] ) : array();

            // Create the post
            $post_id = wp_insert_post( array(
                'post_title' => $post_title,
                'post_content' => $post_content,
                'post_type' => 'item-db',
                'post_status' => 'publish',
            ) );

            // Add custom fields
            foreach ( $post_custom_fields as $post_custom_field ) {
                $field_parts = explode( ': ', $post_custom_field );
                if ( count( $field_parts ) == 2 ) {
                    $field_name = $field_parts[0];
                    $field_value = $field_parts[1];
                    add_post_meta( $post_id, $field_name, $field_value );
                }
            }
        }

        // Close the file
        fclose( $handle );

        // Redirect back to the settings page with a success message
        wp_redirect( add_query_arg( array( 'page' => 'itemdb_settings', 'message' => 'import_success' ), admin_url( 'options-general.php' ) ) );
        exit;
    }

    // If no file was uploaded, redirect back to the settings page with an error message
    wp_redirect( add_query_arg( array( 'page' => 'itemdb_settings', 'message' => 'import_error' ), admin_url( 'options-general.php' ) ) );
    exit;
}