<?php

// Export CSV //
// Add this function to add the 'Export to CSV' option to the bulk actions dropdown.
function item_db_add_export_bulk_action($actions) {
    $actions['export_csv'] = 'Export to CSV';
    return $actions;
}
add_filter('bulk_actions-edit-item-db', 'item_db_add_export_bulk_action');

// Add this function to handle the 'Export to CSV' bulk action.
function item_db_handle_export_bulk_action($redirect_to, $doaction, $post_ids) {
    global $wpdb;

    $meta_keys = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} WHERE post_id IN (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = %s
            ) AND meta_key LIKE %s",
            'db',
            'itemdb_%'
        )
    );

    $csv_data = [];
    $header_row = ['Title', 'Content', 'Category'];
    $header_row = array_merge($header_row, $meta_keys);
    $csv_data[] = $header_row;

    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        $title = $post->post_title;
        $content = $post->post_content;

        // Get the category
        $category = '';
        $terms = wp_get_object_terms($post_id, 'item_category');
        if (!empty($terms) && !is_wp_error($terms)) {
            $category = $terms[0]->slug;
        }

        $row = [$title, $content, $category];

        foreach ($meta_keys as $meta_key) {
            $meta_value = get_post_meta($post_id, $meta_key, true);
            $row[] = $meta_value;
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
        <input type="hidden" name="import_csv" value="1">
        <?php wp_nonce_field('item_db_import_csv', 'item_db_import_csv_nonce'); ?>
        <input type="file" name="csv_file" accept=".csv">
        <input type="submit" value="Import CSV" class="button button-primary">
    </form>
    <?php
}

function custom_sanitize_key($key) {
    return preg_replace('/[^A-Za-z0-9_\-]+/', '', $key);
}

function item_db_set_post_data($post_id, $data) {
    foreach ($data as $key => $value) {
        if ($key === 'Category') {
            // Set the post category.
            $term = get_term_by('slug', $value, 'item_category');
            if ($term) {
                wp_set_object_terms($post_id, $term->term_id, 'item_category');
            }
        } elseif ($key !== 'Title' && $key !== 'Content') {
            update_post_meta($post_id, $key, $value);
        }
    }
}

function item_db_upload_image_from_url($image_url) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $tmp = download_url($image_url);

    if (is_wp_error($tmp)) {
        return false;
    }

    $file_array = array();
    $file_array['name'] = basename($image_url);
    $file_array['tmp_name'] = $tmp;

    $attachment_id = media_handle_sideload($file_array, 0);

    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    return $attachment_id;
}

// Add this function to process the CSV file and import the custom posts.
function item_db_import_csv() {
    if (isset($_POST['import_csv']) && check_admin_referer('item_db_import_csv', 'item_db_import_csv_nonce')) {
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $csv_file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $header = fgetcsv($csv_file);

            // Trim the header keys to remove any leading/trailing spaces.
            $header = array_map('trim', $header);

            while ($row = fgetcsv($csv_file)) {
                $data = array_combine($header, $row);

                // Create custom post type 'item-db' and set post meta.
                $post_id = wp_insert_post([
                    'post_title' => isset($data['Title']) ? $data['Title'] : '',
                    'post_content' => isset($data['Content']) ? $data['Content'] : '',
                    'post_status' => 'publish',
                    'post_type' => 'db',
                ]);

                if (!empty($data['Thumbnail_URL'])) {
                    $attachment_id = item_db_upload_image_from_url($data['Thumbnail_URL']);
                    if ($attachment_id) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }

                if ($post_id !== 0) {
                    item_db_set_post_data($post_id, $data);
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