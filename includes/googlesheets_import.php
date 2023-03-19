<?php
/**
 * Include the Google API PHP client library.
 * See https://developers.google.com/sheets/api/quickstart/php
 */
require_once plugin_dir_path( __FILE__ ) . 'google-api-php-client/vendor/autoload.php';

function itemdb_google_sheets_import( $sheet_id ) {
    // Get the API key from the settings page.
    $api_key = get_option( 'itemdb_google_api_key' );
    if ( empty( $api_key ) ) {
        return new WP_Error( 'missing_api_key', __( 'No API key was found. Please enter your API key on the settings page.', 'itemdb' ) );
    }

    // Authenticate and authorize the API client with the API key.
    $client = new Google_Client();
    $client->setApplicationName( 'ItemDB Google Sheets Import' );
    $client->setDeveloperKey( $api_key );
    $service = new Google_Service_Sheets( $client );

    // Get the data from the Google Sheet.
    try {
        $response = $service->spreadsheets_values->get( $sheet_id, 'Sheet1' );
        $values = $response->getValues();
        if ( empty( $values ) ) {
            return new WP_Error( 'no_data', __( 'No data was found in the Google Sheet.', 'itemdb' ) );
        }

        // Import the data into the item database.
        foreach ( $values as $row ) {
            $post_args = array(
                'post_title' => $row[0],
                'post_type' => 'item-db',
                'post_status' => 'publish',
            );
            $post_id = wp_insert_post( $post_args );
            if ( ! is_wp_error( $post_id ) ) {
                // Add custom fields to the post.
                update_field( 'field_itemdb_type', $row[1], $post_id );
                update_field( 'field_itemdb_description', $row[2], $post_id );
            }
        }

        return true;

    } catch ( Google_Service_Exception $e ) {
        return new WP_Error( 'google_error', sprintf( __( 'There was an error retrieving the data from the Google Sheet: %s', 'itemdb' ), $e->getMessage() ) );
    } catch ( Exception $e ) {
        return new WP_Error( 'import_error', sprintf( __( 'There was an error importing the data: %s', 'itemdb' ), $e->getMessage() ) );
    }
}


