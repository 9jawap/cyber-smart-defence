<?php
/**
 * Plugin Name: Cyber Smart Defence
 * Plugin URI: https://cybersmartempire.com/cyberdefence/
 * Description: Lightweight WordPress security firewall with login protection, and threat monitoring.
 * Version: 3.1.2
 * Author: Cyber Smart Empire
 * License: GPLv3
 * Text Domain: cyber-smart-defence
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CSD_PATH', plugin_dir_path( __FILE__ ) );
define( 'CSD_URL', plugin_dir_url( __FILE__ ) );

// Core includes
require_once CSD_PATH . 'includes/firewall.php';
require_once CSD_PATH . 'includes/login-protection.php';
require_once CSD_PATH . 'includes/admin-page.php';

// Optional API client
require_once CSD_PATH . 'includes/api-client.php';

// Enqueue admin scripts & styles
add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_style( 'csd-admin', CSD_URL . 'assets/admin.css', [], '3.1.2' );
    wp_enqueue_script( 'csd-admin', CSD_URL . 'assets/admin.js', ['jquery'], '3.1.2', true );
});

// Run firewall early
add_action( 'init', 'csd_run_firewall', 0 );

// Activation redirect (fixed)
register_activation_hook( __FILE__, function() {
    update_option( 'csd_do_activation_redirect', true );
});

// Redirect after menu is added
add_action( 'admin_menu', function() {
    // Add the plugin menu first
    add_menu_page(
        'Cyber Defence',
        'Cyber Defence',
        'manage_options',
        'cyber-smart-defence',
        'csd_admin_page',
        'dashicons-shield-alt',
        80
    );

    // Now do the redirect safely
    if ( get_option( 'csd_do_activation_redirect' ) ) {
        delete_option( 'csd_do_activation_redirect' );
        if ( current_user_can( 'manage_options' ) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=cyber-smart-defence' ) );
            exit;
        }
    }
});

// Optional: Admin notices for activation & status
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $ready = function_exists( 'csd_run_firewall' );

    if ( $ready ) {
        echo '<div class="notice notice-success is-dismissible">
            <p><strong>🛡 Cyber Smart Defence is active.</strong> Your website is protected in real time.</p>
        </div>';
    } else {
        echo '<div class="notice notice-warning">
            <p><strong>⚠ Cyber Smart Defence is not fully activated.</strong> Activate protection from the plugin page.</p>
        </div>';
    }
});
