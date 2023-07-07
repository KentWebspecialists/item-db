<?php
// Settings Section //

function itemdb_register_settings() {
    // Register general settings
    register_setting('itemdb_options', 'itemdb_google_api_key');
    register_setting('itemdb_options', 'itemdb_services_api_key');

    // Register pagination settings
    register_setting('itemdb_options', 'itemdb_enable_pagination');
    add_settings_section(
        'itemdb_pagination_settings_section',
        '',
        '',
        'itemdb_options'
    );
    add_settings_field(
        'itemdb_enable_pagination',
        'Enable Pagination',
        'itemdb_enable_pagination_render',
        'itemdb_options',
        'itemdb_pagination_settings_section'
    );
}
add_action('admin_init', 'itemdb_register_settings');

function itemdb_add_settings_menu() {
    add_options_page('ItemDB Settings', 'ItemDB Settings', 'manage_options', 'itemdb-settings', 'itemdb_settings_page');
}
add_action('admin_menu', 'itemdb_add_settings_menu');

function itemdb_settings_page() {
    ?>
    <div class="wrap">
        <form method="post" action="options.php" class="itemdb-section card">
        <h1>ItemDB Settings</h1>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google API Key</th>
                    <td><input type="text" name="itemdb_google_api_key" value="<?php echo esc_attr(get_option('itemdb_google_api_key')); ?>" /></td>
                </tr>
            </table>
            <h3>Configuration</h3>
            <?php
                settings_fields('itemdb_options');
                do_settings_sections('itemdb_options');
            ?>
            <?php submit_button(); ?>
        </form>
        <div class="itemdb-section card">
            <?php item_db_csv_upload_form(); ?>
        </div>
    </div>
    <?php
}

function itemdb_custom_single_template($single_template) {
    global $post;

    // Checks for single template by post type
    if ($post->post_type == 'db') {
        if ( file_exists( plugin_dir_path( __FILE__ ) . 'post-templates/post-data.php' ) ) {
            return plugin_dir_path( __FILE__ ) . 'post-templates/post-data.php';
        }
    }

    return $single_template;
}

add_filter( 'single_template', 'itemdb_custom_single_template' );