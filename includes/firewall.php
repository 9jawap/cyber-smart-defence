<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Safe settings helpers.
 */
function csd_get_settings() {
    $defaults = [
        'enabled' => true,
        'lockout_threshold' => 5,
        'lockout_duration_minutes' => 30,
        'block_suspicious_agents' => true,
        'log_limit' => 20,
    ];

    $saved = get_option( 'csd_settings', [] );
    if ( ! is_array( $saved ) ) {
        $saved = [];
    }

    $settings = array_merge( $defaults, $saved );
    $settings['enabled'] = ! empty( $settings['enabled'] );
    $settings['block_suspicious_agents'] = ! empty( $settings['block_suspicious_agents'] );
    $settings['lockout_threshold'] = max( 1, absint( $settings['lockout_threshold'] ) );
    $settings['lockout_duration_minutes'] = max( 1, absint( $settings['lockout_duration_minutes'] ) );
    $settings['log_limit'] = max( 5, absint( $settings['log_limit'] ) );

    return $settings;
}

function csd_is_protection_enabled() {
    return csd_get_settings()['enabled'];
}

/**
 * Admin page
 */
function csd_admin_page() {
    global $wpdb;

    if ( isset( $_POST['csd_save_settings'] ) ) {
        check_admin_referer( 'csd_save_settings', 'csd_settings_nonce' );

        $settings = csd_get_settings();
        $settings['enabled'] = ! empty( $_POST['csd_enabled'] );
        $settings['block_suspicious_agents'] = ! empty( $_POST['csd_block_suspicious_agents'] );
        $settings['lockout_threshold'] = max( 1, absint( $_POST['csd_lockout_threshold'] ?? $settings['lockout_threshold'] ) );
        $settings['lockout_duration_minutes'] = max( 1, absint( $_POST['csd_lockout_duration_minutes'] ?? $settings['lockout_duration_minutes'] ) );
        $settings['log_limit'] = max( 5, absint( $_POST['csd_log_limit'] ?? $settings['log_limit'] ) );

        update_option( 'csd_settings', $settings );
        echo '<div class="notice notice-success is-dismissible"><p><strong>Cyber Smart Defence:</strong> Settings saved successfully.</p></div>';
    }

    $settings = csd_get_settings();
    $total_logs = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}csd_threat_logs"
    );
    $blocked_ips = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT ip) FROM {$wpdb->prefix}csd_threat_logs WHERE type IN ('Login Lockout', 'Firewall Block')"
    );
    ?>
    <div class="wrap csd-wrap">
        <h1>🛡 Cyber Smart Defence</h1>

        <div class="csd-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin:20px 0;">
            <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <h2 style="margin-top:0;">Protection</h2>
                <p><strong>Status:</strong> <?php echo $settings['enabled'] ? '<span style="color:#0a8f3d;">Enabled</span>' : '<span style="color:#d63638;">Disabled</span>'; ?></p>
                <p><strong>Threats logged:</strong> <?php echo esc_html( $total_logs ); ?></p>
            </div>

            <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <h2 style="margin-top:0;">Security Rule</h2>
                <p><strong>Lockout threshold:</strong> <?php echo esc_html( $settings['lockout_threshold'] ); ?> failed attempts</p>
                <p><strong>Lockout duration:</strong> <?php echo esc_html( $settings['lockout_duration_minutes'] ); ?> minutes</p>
            </div>

            <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <h2 style="margin-top:0;">Visibility</h2>
                <p><strong>Blocked IPs:</strong> <?php echo esc_html( $blocked_ips ); ?></p>
                <p><strong>Log window:</strong> <?php echo esc_html( $settings['log_limit'] ); ?> recent events</p>
            </div>
        </div>

        <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-bottom:20px;">
            <h2>⚙️ Protection Settings</h2>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=cyber-smart-defence' ) ); ?>">
                <?php wp_nonce_field( 'csd_save_settings', 'csd_settings_nonce' ); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="csd_enabled">Enable protection</label></th>
                            <td>
                                <input type="checkbox" id="csd_enabled" name="csd_enabled" value="1" <?php checked( $settings['enabled'], true ); ?> />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="csd_lockout_threshold">Failed login threshold</label></th>
                            <td>
                                <input type="number" id="csd_lockout_threshold" name="csd_lockout_threshold" min="1" max="50" value="<?php echo esc_attr( $settings['lockout_threshold'] ); ?>" class="small-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="csd_lockout_duration_minutes">Lockout duration (minutes)</label></th>
                            <td>
                                <input type="number" id="csd_lockout_duration_minutes" name="csd_lockout_duration_minutes" min="1" max="1440" value="<?php echo esc_attr( $settings['lockout_duration_minutes'] ); ?>" class="small-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="csd_block_suspicious_agents">Block suspicious user agents</label></th>
                            <td>
                                <input type="checkbox" id="csd_block_suspicious_agents" name="csd_block_suspicious_agents" value="1" <?php checked( $settings['block_suspicious_agents'], true ); ?> />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="csd_log_limit">Recent log entries</label></th>
                            <td>
                                <input type="number" id="csd_log_limit" name="csd_log_limit" min="5" max="100" value="<?php echo esc_attr( $settings['log_limit'] ); ?>" class="small-text" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" name="csd_save_settings" value="1" class="button button-primary">Save settings</button>
                </p>
            </form>
        </div>

        <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-bottom:20px;">
            <h2>
                Engine:
                <span class="csd-status on">● Advanced Protection</span>
            </h2>

            <p>
                Cyber Smart Defence manages real-time threat protection, failed-login defenses, and suspicious traffic filtering from a centralized configuration panel.
            </p>

            <a href="https://cybersmartempire.com/cyberdefence/"
               target="_blank"
               rel="noopener"
               class="button button-primary button-hero">
                🚀 Upgrade to Full Protection
            </a>
        </div>

        <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-bottom:20px;">
            <h2>🚨 Threat Logs</h2>
            <p class="description">Recent security activity detected by Cyber Defence</p>
            <?php csd_render_threat_logs(); ?>
        </div>

        <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <h2>📢 Cyber Smart Empire</h2>
            <p>Learn actionable cyber defense strategies from our bestselling guides!</p>

            <div style="max-width:420px;">
                <img
                    src="<?php echo esc_url( plugins_url( '../assets/cover.gif', __FILE__ ) ); ?>"
                    alt="Cyber Smart Empire"
                    style="width:100%;height:auto;border-radius:6px;border:1px solid #ddd;"
                >
            </div>

            <p style="margin-top:10px;">
                <a href="https://cybersmartempire.com" target="_blank" rel="noopener" class="button button-primary">
                    🚀 Visit Cyber Smart Empire
                </a>
            </p>
        </div>

        <div class="csd-card" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-top:20px;">
            <h2>📞 Support & Docs</h2>
            <p>
                <a href="https://cybersmartempire.com/consultation/" target="_blank" rel="noopener">Contact Support</a><br>
                <a href="https://cybersmartempire.com/docs/" target="_blank" rel="noopener">View Documentation</a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Render Threat Logs
 */
function csd_render_threat_logs() {
    global $wpdb;

    $settings = csd_get_settings();
    $limit = max( 5, absint( $settings['log_limit'] ) );
    $cache_key = 'csd_threat_logs_' . $limit;
    $logs = wp_cache_get( $cache_key, 'cyber-smart-defence' );

    if ( false === $logs ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, created_at, ip, type, action_taken
                 FROM {$wpdb->prefix}csd_threat_logs
                 ORDER BY created_at DESC
                 LIMIT %d",
                $limit
            )
        );

        wp_cache_set( $cache_key, $logs, 'cyber-smart-defence', 5 * MINUTE_IN_SECONDS );
    }

    if ( empty( $logs ) ) {
        echo '<p>No threat events logged yet.</p>';
        return;
    }

    echo '<div class="csd-log-wrapper">';

    foreach ( $logs as $log ) {
        echo '<div class="csd-log-item">';
        echo '<button type="button" class="csd-log-toggle">
                <span>' . esc_html( $log->ip ) . '</span>
                <span>Tap to view</span>
              </button>';

        echo '<div class="csd-log-details">';
        echo '<p><strong>Date:</strong> ' . esc_html( $log->created_at ) . '</p>';
        echo '<p><strong>Threat:</strong> ' . esc_html( $log->type ) . '</p>';
        echo '<p><strong>Action:</strong> ' . esc_html( $log->action_taken ) . '</p>';
        echo '<a class="button" target="_blank" href="https://ipinfo.io/' . esc_attr( $log->ip ) . '">🔎 Trace IP</a>';
        echo '</div></div>';
    }

    echo '</div>';
    ?>
    <style>
        .csd-log-wrapper{display:flex;flex-direction:column;gap:10px}
        .csd-log-item{border:1px solid #ddd;border-radius:6px;overflow:hidden}
        .csd-log-toggle{width:100%;padding:10px;background:#f5f5f5;border:none;font-weight:bold;cursor:pointer}
        .csd-log-details{display:none;padding:10px}
        @media(min-width:768px){
            .csd-log-toggle{display:none}
            .csd-log-details{display:block!important}
        }
    </style>

    <script>
    jQuery(function($){
        $('.csd-log-toggle').on('click', function(){
            $(this).next('.csd-log-details').slideToggle();
        });
    });
    </script>
    <?php
}
