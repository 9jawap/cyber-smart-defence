<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Handle login failures and lockout.
 */
add_action( 'wp_login_failed', 'csd_login_fail' );

function csd_login_fail() {
    if ( ! csd_is_protection_enabled() ) {
        return;
    }

    $settings = csd_get_settings();
    $threshold = max( 1, absint( $settings['lockout_threshold'] ) );

    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    if ( empty( $ip ) ) return;

    $key = 'csd_fail_' . md5( $ip );
    $fails = (int) get_transient( $key );
    $fails++;
    set_transient( $key, $fails, 15 * MINUTE_IN_SECONDS );

    if ( $fails >= $threshold ) {
        $lockout_minutes = max( 1, absint( $settings['lockout_duration_minutes'] ) );
        set_transient( 'csd_block_' . md5( $ip ), 1, $lockout_minutes * MINUTE_IN_SECONDS );

        global $wpdb;
        $table  = $wpdb->prefix . 'csd_threat_logs';
        $charset_collate = $wpdb->get_charset_collate();

        if ( ! get_transient( 'csd_table_created' ) ) {
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            $sql = "CREATE TABLE `$table` (
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

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $table,
            [
                'ip'           => $ip,
                'type'         => 'Login Lockout',
                'action_taken' => 'Too many failed login attempts'
            ],
            ['%s', '%s', '%s']
        );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
    }
}

/**
 * Block IP if currently locked out.
 */
add_action( 'login_init', function () {
    if ( ! csd_is_protection_enabled() ) {
        return;
    }

    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    if ( empty( $ip ) ) return;

    if ( get_transient( 'csd_block_' . md5( $ip ) ) ) {
        wp_die(
            '<h1>' . esc_html__( 'Access Temporarily Locked', 'cyber-smart-defence' ) . '</h1>
            <p>' . esc_html__( 'Too many failed login attempts.', 'cyber-smart-defence' ) . '</p>',
            esc_html__( 'Cyber Smart Defence', 'cyber-smart-defence' )
        );
    }
});
