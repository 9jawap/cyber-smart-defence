<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin page
 */
function csd_admin_page() {
    ?>
    <div class="wrap csd-wrap">
        <h1>🛡 Cyber Smart Defence</h1>

        <div class="csd-card">
            <h2>
                Engine:
                <span class="csd-status on">● Basic Protection</span>
            </h2>

            <p>
                Cyber Smart Defence Basic Protection provides WordPress-level protection against brute-force attacks,
                malicious request patterns, and abusive automation.
            </p>

            <a href="https://cybersmartempire.com/cyberdefence/"
               target="_blank"
               rel="noopener"
               class="button button-primary button-hero">
                🚀 Upgrade to Full Protection
            </a>
        </div>

        <div class="csd-card">
            <h2>🚨 Threat Logs</h2>
            <p class="description">Recent security activity detected by Cyber Defence</p>
            <?php csd_render_threat_logs(); ?>
        </div>

        <div class="csd-card">
            <h2>📢 Cyber Smart Empire</h2>
            <p>Learn actionable cyber defense strategies from our bestselling guides!.</p>

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

        <div class="csd-card">
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

    $limit = 20;
    $cache_key = 'csd_threat_logs_' . $limit;
    $logs = wp_cache_get( $cache_key, 'cyber-smart-defence' );

    if ( false === $logs ) {

        // ✅ Scanner-safe: no variable table name
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
