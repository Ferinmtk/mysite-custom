<?php
/**
 * Plugin Name: MySite Custom
 * Description: Custom functionality for Ferin’s sandbox site. Adds CSS/JS and Gravity Forms webhook integration.
 * Version: 1.0.0
 * Author: Ferin
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue custom CSS and JS
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'mysite-custom-css',
        plugin_dir_url(__FILE__) . 'assets/css/custom.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'mysite-custom-js',
        plugin_dir_url(__FILE__) . 'assets/js/custom.js',
        [],
        '1.0.0',
        true
    );
});

/**
 * Gravity Forms after submission → send data to Webhook.site
 */
add_action('gform_after_submission', function ($entry, $form) {
    $payload = [
        'form_id'      => $form['id'],
        'entry_id'     => $entry['id'],
        'name'         => rgar($entry, '1'), // adjust field IDs
        'email'        => rgar($entry, '2'),
        'message'      => rgar($entry, '5'),
        'submitted_at' => current_time('c'),
    ];

    wp_remote_post('https://webhook.site/YOUR-UNIQUE-ID', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);
}, 10, 2);
