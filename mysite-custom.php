<?php
/**
 * Plugin Name: MySite Custom
 * Version: 1.0.0

if (!defined('ABSPATH')) exit;

/**
 * Enqueue custom CSS and JS
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'mysite-custom-css',
        plugin_dir_url(__FILE__) . 'assets/css/custom.css',
        [],
    );

    wp_enqueue_script(
        'mysite-custom-js',
        plugin_dir_url(__FILE__) . 'assets/js/custom.js',
        [],
        true
    );
});

/**
 * Gravity Forms after submission → send data to Webhook.site
 */
    $payload = [
        'submitted_at' => current_time('c'),
    ];

        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);
