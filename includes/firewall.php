<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Run firewall checks on every frontend request
 */
function csd_run_firewall() {
    if ( is_admin() ) return;

    // Sanitize inputs
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $query       = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
    $user_agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    $ip          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

    $payload = strtolower( $request_uri . ' ' . $query );

    // Basic malicious patterns
    $rules = [
        'union select',
        'base64_decode',
        '<script',
        '../',
        'eval(',
        'information_schema',
        'load_file',
        'benchmark(',
        'sleep('
    ];

    foreach ( $rules as $rule ) {
        if ( strpos( $payload, strtolower( $rule ) ) !== false ) {
            csd_block_request( 'Malicious pattern detected', $ip );
        }
    }

    // Block suspicious user agents
    if ( empty( $user_agent ) || strlen( $user_agent ) < 4 ) {
        csd_block_request( 'Suspicious user agent', $ip );
    }
}

/**
 * Block requests and log threats
 */
function csd_block_request( $reason, $ip ) {
    global $wpdb;

    // Internal plugin table, safe to use
    $table  = $wpdb->prefix . 'csd_threat_logs';
    $charset_collate = $wpdb->get_charset_collate();

    // Create threat logs table safely (once every 12 hours)
    if ( ! get_transient( 'csd_table_created' ) ) {

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            ip VARCHAR(45) NOT NULL,
            type VARCHAR(100) NOT NULL,
            action_taken VARCHAR(255) NOT NULL
        ) $charset_collate;";
        dbDelta( $sql );
        set_transient( 'csd_table_created', 1, 12 * HOUR_IN_SECONDS );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
    }

    // Insert threat log safely
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
    $inserted = $wpdb->insert(
        $table,
        [
            'ip'           => $ip,
            'type'         => 'Firewall Block',
            'action_taken' => $reason
        ],
        ['%s','%s','%s']
    );
    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery

    // Cache inserted row
    if ( $inserted ) {
        wp_cache_set( $wpdb->insert_id, [
            'ip'           => $ip,
            'type'         => 'Firewall Block',
            'action_taken' => $reason
        ], 'csd_threat_logs' );
    }

    // Block access immediately
    status_header( 403 );
    exit( esc_html__( 'Access denied.', 'cyber-smart-defence' ) );
}

// Hook firewall into WordPress
add_action( 'init', 'csd_run_firewall' );
