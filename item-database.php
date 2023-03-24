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


// Post Type Registration //

require_once plugin_dir_path(__FILE__) . 'includes/itemdb-shortcode.php';
function itemdb_enqueue_frontend_scripts() {
    wp_enqueue_style('itemdb-style', plugin_dir_url(__FILE__) . 'includes/styles.css', array(), '1.0.0');
    wp_enqueue_script('itemdb-script', plugin_dir_url(__FILE__) . 'includes/itemdb.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'itemdb_enqueue_frontend_scripts');

function itemdb_enqueue_scripts() {
    wp_enqueue_script('itemdb-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), '1.0.0', true);
}

add_action('admin_enqueue_scripts', 'itemdb_enqueue_scripts');

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


function itemdb_custom_fields_callback($post) {
    wp_nonce_field('itemdb_save_custom_fields', 'itemdb_custom_fields_nonce');
    $custom_fields = get_post_meta($post->ID, '_itemdb_custom_fields', true);

    echo '<div id="itemdb-custom-fields-container">';
    if (!empty($custom_fields)) {
        foreach ($custom_fields as $field) {
            echo '<div class="itemdb-custom-field">';
            echo '<input type="text" name="_itemdb_custom_fields[][label]" placeholder="Label" value="' . esc_attr($field['label']) . '">';
            echo '<input type="text" name="_itemdb_custom_fields[][value]" placeholder="Value" value="' . esc_attr($field['value']) . '">';
            echo '<a href="#" class="itemdb-remove-field button">Remove</a>';
            echo '</div>';
        }
    }
    echo '</div>';
    echo '<p><a href="#" id="itemdb-add-field" class="button">Add Field</a></p>';

    // Add a template for new custom fields
    echo '<div id="itemdb-custom-field-template" style="display:none;">';
    echo '<div class="itemdb-custom-field">';
    echo '<input type="text" name="_itemdb_custom_fields[][label]" placeholder="Label">';
    echo '<input type="text" name="_itemdb_custom_fields[][value]" placeholder="Value">';
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

    $custom_fields = isset($_POST['_itemdb_custom_fields']) ? (array) $_POST['_itemdb_custom_fields'] : array();
    $sanitized_fields = array();
   
    foreach ($custom_fields as $field) {
        $sanitized_fields[] = array(
            'label' => sanitize_text_field($field['label']),
            'value' => sanitize_text_field($field['value']),
        );
    }

    update_post_meta($post_id, '_itemdb_custom_fields', $sanitized_fields);
    $saved_fields = get_post_meta($post_id, '_itemdb_custom_fields', true);
}

add_action('save_post', 'itemdb_save_custom_fields');

add_shortcode('ItemDB', 'itemdb_display_items');

// Settings Section //

// Modify the itemdb_settings_page() function to include the CSV upload form and the API key field.
function itemdb_settings_page() {
    ?>
    <div class="wrap">
        <h1>ItemDB Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('itemdb_options');
            do_settings_sections('itemdb_options');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google API Key</th>
                    <td><input type="text" name="itemdb_google_api_key" value="<?php echo esc_attr(get_option('itemdb_google_api_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php item_db_csv_upload_form(); ?>
    </div>
    <?php
}

function itemdb_register_settings() {
    register_setting('itemdb_options', 'itemdb_google_api_key');
    register_setting('itemdb_options', 'itemdb_services_api_key');
}
add_action('admin_init', 'itemdb_register_settings');

function itemdb_add_settings_menu() {
    add_options_page('ItemDB Settings', 'ItemDB Settings', 'manage_options', 'itemdb-settings', 'itemdb_settings_page');
}
add_action('admin_menu', 'itemdb_add_settings_menu');

// Export CSV //

// Add this function to add the 'Export to CSV' option to the bulk actions dropdown.
function item_db_add_export_bulk_action($actions) {
    $actions['export_csv'] = 'Export to CSV';
    return $actions;
}
add_filter('bulk_actions-edit-item-db', 'item_db_add_export_bulk_action');

// Add this function to handle the 'Export to CSV' bulk action.
function item_db_handle_export_bulk_action($redirect_to, $doaction, $post_ids) {
    global $wpdb; // Make the $wpdb object available within the function.

    if ($doaction !== 'export_csv') {
        return $redirect_to;
    }

    // Get all the custom fields' meta keys associated with the 'item-db' post type.
    $meta_keys = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} WHERE post_id IN (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = %s
            ) AND meta_key NOT IN ('title', 'content')",
            'item-db'
        )
    );

    $csv_data = [];
    $header_row = ['Title', 'Content'];
    $header_row = array_merge($header_row, $meta_keys);
    $csv_data[] = $header_row;

    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        $title = $post->post_title;
        $content = $post->post_content;

        $row = [$title, $content];

        // Loop through the custom fields and fetch their values for the current post.
        foreach ($meta_keys as $meta_key) {
            if ($meta_key !== 'title' && $meta_key !== 'content') {
                $meta_value = get_post_meta($post_id, $meta_key, true);
                $row[] = $meta_value;
            }
        }

        $csv_data[] = $row;
    }

    $csv_file = fopen('php://memory', 'w');
    foreach ($csv_data as $row) {
        fputcsv($csv_file, $row);
    }

    fseek($csv_file, 0);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=item-db-export.csv');
    fpassthru($csv_file);
    fclose($csv_file);
    exit;
}

add_filter('handle_bulk_actions-edit-item-db', 'item_db_handle_export_bulk_action', 10, 3);


// Import CSV //

// Add this function to create the CSV upload form.
function item_db_csv_upload_form() {
    ?>
    <h2>Import CSV</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv">
        <input type="submit" name="import_csv" value="Import">
        <?php wp_nonce_field('import_csv_nonce', 'import_csv_nonce_field'); ?>
    </form>
    <?php
}

function custom_sanitize_key($key) {
    return preg_replace('/[^A-Za-z0-9_\-]+/', '', $key);
}

// Add this function to process the CSV file and import the custom posts.
function item_db_import_csv() {
    if (isset($_POST['import_csv']) && check_admin_referer('import_csv_nonce', 'import_csv_nonce_field')) {
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $csv_file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $header = fgetcsv($csv_file);
            $sanitized_header = array_map('custom_sanitize_key', $header); // Use custom_sanitize_key function.

            while ($row = fgetcsv($csv_file)) {
                $data = array_combine($sanitized_header, $row);

                // Create custom post type 'item-db' and set post meta.
                $post_id = wp_insert_post([
                    'post_title' => $data['title'],
                    'post_content' => isset($data['content']) ? $data['content'] : '',
                    'post_status' => 'publish',
                    'post_type' => 'item-db',
                ]);

                if ($post_id !== 0) {
                    foreach ($data as $key => $value) {
                        if (!in_array($key, ['title', 'content'])) {
                            update_post_meta($post_id, $key, $value);
                        }
                    }
                }
            }

            fclose($csv_file);
            echo '<div class="notice notice-success is-dismissible"><p>CSV imported successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Please upload a CSV file.</p></div>';
        }
    }
}

// Add the 'item_db_import_csv' function to the 'admin_init' hook.
add_action('admin_init', 'item_db_import_csv');