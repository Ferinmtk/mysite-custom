<?php
/**
 * Plugin Name: MySite Custom
 * Description: Custom functionality for Ferin’s sandbox site. Adds CSS/JS and integrates Gravity Forms with Webhook.site.
 * Version: 1.0.0
 * Author: Ferin
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

define('MYSITE_CUSTOM_VERSION', '1.0.0');

/**
 * Enqueue custom CSS and JS
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'mysite-custom-css',
        plugin_dir_url(__FILE__) . 'assets/css/custom.css',
        [],
        MYSITE_CUSTOM_VERSION
    );

    wp_enqueue_script(
        'mysite-custom-js',
        plugin_dir_url(__FILE__) . 'assets/js/custom.js',
        [],
        MYSITE_CUSTOM_VERSION,
        true
    );
});

/**
 * Gravity Forms after submission → send data to Webhook.site
 */
function mysite_send_to_webhook($entry, $form) {
    $payload = [
        'form_id'      => (int) $form['id'],
        'entry_id'     => (int) $entry['id'],
        'name'         => [
            'first' => rgar($entry, '1.3'),
            'last'  => rgar($entry, '1.6'),
        ],
        'preferred_contact' => rgar($entry, '3'),
        'email'        => rgar($entry, '4'),
        'confirm_email'=> rgar($entry, '5'),
        'phone'        => rgar($entry, '6'),
        'best_time'    => rgar($entry, '7'),
        'message'      => rgar($entry, '8'),
        'submitted_at' => current_time('c'),
        'site'         => [
            'name' => get_bloginfo('name'),
            'url'  => home_url('/'),
        ],
    ];

    $response = wp_remote_post('https://webhook.site/a7e9b24c-ad99-46c4-a66e-6ddad7dbed12', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);

    // Optional: log to debug.log for testing
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Gravity Forms Webhook Payload: ' . wp_json_encode($payload));
        if (is_wp_error($response)) {
            error_log('Webhook Error: ' . $response->get_error_message());
        } else {
            error_log('Webhook Response: ' . wp_remote_retrieve_body($response));
        }
    }
}
add_action('gform_after_submission', 'mysite_send_to_webhook', 10, 2);
