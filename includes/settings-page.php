<?php

function itemdb_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <form action="options.php" method="post">
            <?php
                // Output security fields
                settings_fields('itemdb_options');

                // Output setting sections
                do_settings_sections('itemdb_settings');

                // Submit button
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

function itemdb_google_api_key_field() {
    echo '<input type="text" id="itemdb_google_api_key" name="itemdb_google_api_key" value="' . esc_attr(get_option('itemdb_google_api_key')) . '" />';
}

require_once( plugin_dir_path( __FILE__ ) . '../item-database.php' );
