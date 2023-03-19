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

                // Submit button (Save Changes)
                submit_button("This Button will save the settings, so should really be kept for settings only");
            ?>
        </form>
    </div>
    <?php
}
require_once( plugin_dir_path( __FILE__ ) . '../item-database.php' );