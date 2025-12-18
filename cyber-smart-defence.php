<?php
/**
 * Plugin Name: Cyber Smart Defence
 * Plugin URI: https://cybersmartempire.com/cyberdefence/
 * Description: Free enterprise-grade website protection by Cyber Smart Empire. Users can install manually, via one-click, or request professional setup.
 * Version: 2.2
 * Author: Cyber Smart Empire
 * Author URI: https://cybersmartempire.com
 * License: GPLv3
 * Text Domain: cyber-smart-defence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// REDIRECT TO PLUGIN PAGE ON ACTIVATION (SAFE)
// -----------------------------------------------------------------------------
register_activation_hook( __FILE__, function () {
    add_option( 'csd_do_activation_redirect', true );
});

add_action( 'admin_init', function () {
    if ( is_admin() && get_option( 'csd_do_activation_redirect' ) ) {
        delete_option( 'csd_do_activation_redirect' );
        wp_safe_redirect( admin_url( 'admin.php?page=cyber-smart-defence' ) );
        exit;
    }
});

// -----------------------------------------------------------------------------
// CHECK IF ROOT SECURITY ENGINE EXISTS
// -----------------------------------------------------------------------------
function csd_security_system_ready() {
    $path = ABSPATH . 'security/';
    return is_dir( $path )
        && file_exists( $path . 'config.php' )
        && file_exists( $path . 'project-security.php' );
}

// -----------------------------------------------------------------------------
// AUTO-INTEGRATE SECURITY ENGINE (FRONTEND ONLY)
// -----------------------------------------------------------------------------
add_action( 'init', function () {
    if ( ! is_admin() && csd_security_system_ready() ) {
        include_once ABSPATH . 'security/config.php';
        include_once ABSPATH . 'security/project-security.php';
    }
}, 1 );

// -----------------------------------------------------------------------------
// AJAX: FETCH & EXTRACT SECURITY ENGINE (NONCE SAFE)
// -----------------------------------------------------------------------------
add_action( 'wp_ajax_csd_fetch_security', function () {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    check_ajax_referer( 'csd_install_nonce', 'nonce' );

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

    WP_Filesystem();
    global $wp_filesystem;

    $zip_url = 'https://cybersmartempire.com/enviroment/security.zip';
    $dest    = ABSPATH . 'security/';

    $tmp = download_url( $zip_url );
    if ( is_wp_error( $tmp ) ) {
        wp_send_json_error( 'Download failed' );
    }

    if ( ! $wp_filesystem->is_dir( $dest ) ) {
        $wp_filesystem->mkdir( $dest );
    }

    $zip = new PclZip( $tmp );
    if ( $zip->extract( PCLZIP_OPT_PATH, $dest ) === 0 ) {
        wp_delete_file( $tmp );
        wp_send_json_error( $zip->errorInfo( true ) );
    }

    wp_delete_file( $tmp );

    wp_send_json_success( array(
        'installer_url' => site_url( '/security/' ),
    ) );
});

// -----------------------------------------------------------------------------
// ADMIN NOTICES
// -----------------------------------------------------------------------------
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( csd_security_system_ready() ) {
        echo '<div class="notice notice-success is-dismissible">
            <p><strong>🛡 Cyber Smart Defence is active.</strong> Your website is protected in real time.</p>
        </div>';
    } else {
        echo '<div class="notice notice-warning">
            <p><strong>⚠ Cyber Smart Defence engine not installed.</strong> Activate protection from the plugin page.</p>
        </div>';
    }
});

// -----------------------------------------------------------------------------
// ADMIN MENU
// -----------------------------------------------------------------------------
add_action( 'admin_menu', function () {
    add_menu_page(
        'Cyber Defence',
        'Cyber Defence',
        'manage_options',
        'cyber-smart-defence',
        'csd_admin_page',
        'dashicons-shield-alt',
        80
    );
});

// -----------------------------------------------------------------------------
// DASHBOARD NEWS & UPDATES WIDGET
// -----------------------------------------------------------------------------
add_action( 'wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'csd_dashboard_news',
        '🛡 Cyber Smart Defence – News & Updates',
        'csd_render_dashboard_news'
    );
});

function csd_render_dashboard_news() {

    include_once ABSPATH . WPINC . '/feed.php';
    $feed = fetch_feed( 'https://cybersmartempire.com/blog/feed/' );

    if ( is_wp_error( $feed ) ) {
        echo '<p>Unable to load updates.</p>';
        return;
    }

    $items = $feed->get_items( 0, 5 );
    if ( empty( $items ) ) {
        echo '<p>No updates available.</p>';
        return;
    }

    echo '<ul>';
    foreach ( $items as $item ) {
        echo '<li style="margin-bottom:10px;">
            <a href="' . esc_url( $item->get_permalink() ) . '" target="_blank">' .
            esc_html( $item->get_title() ) .
            '</a><br>
            <small>' . esc_html( $item->get_date( 'F j, Y' ) ) . '</small>
        </li>';
    }
    echo '</ul>';
}

// -----------------------------------------------------------------------------
// ADMIN PAGE UI (FULL FEATURE SET RESTORED)
// -----------------------------------------------------------------------------
function csd_admin_page() {

    $ready = csd_security_system_ready();
    $panel = site_url( '/security/' );
    $nonce = wp_create_nonce( 'csd_install_nonce' );
    ?>

    <div class="wrap">
        <h1>🛡 Cyber Smart Defence</h1>

        <div class="card" style="max-width:1000px;padding:25px;margin-bottom:20px;">
            <h2>Status:
                <?php if ( $ready ) : ?>
                    <span style="color:#0a7d00;">● Active & Protecting</span>
                <?php else : ?>
                    <span style="color:#b32d2e;">● Protection Not Enabled</span>
                <?php endif; ?>
            </h2>

            <?php if ( $ready ) : ?>
                <p><strong>Your website is actively protected.</strong></p>
                <ul style="list-style:disc;padding-left:20px;">
                    <li>Real-time blocking of malicious traffic</li>
                    <li>Protection against common attack vectors</li>
                    <li>Reduced server load from automated abuse</li>
                    <li>Silent operation without performance impact</li>
                </ul>

                <a href="<?php echo esc_url( $panel ); ?>" target="_blank"
                   class="button button-primary button-hero">
                    Open Security Control Panel
                </a>
            <?php else : ?>
                <p><strong>Your site is currently exposed.</strong> Activate Cyber Smart Defence to unlock:</p>
                <ul style="list-style:disc;padding-left:20px;">
                    <li>Enterprise-grade firewall protection</li>
                    <li>Admin & login abuse prevention</li>
                    <li>Automated malicious traffic blocking</li>
                    <li>Improved stability & uptime</li>
                </ul>

                <button id="csd_install_btn" class="button button-primary button-hero">
                    🚀 One-Click Install & Activate Protection
                </button>

                <div id="csd_loader" style="display:none;margin-top:15px;">
                    <div style="background:#e5e5e5;height:6px;border-radius:3px;">
                        <div id="csd_bar" style="width:0%;height:6px;background:#2271b1;"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" style="max-width:1000px;padding:25px;">
            <h2>📞 Support & Assistance</h2>
            <ul style="list-style:disc;padding-left:20px;">
                <li>Email: <a href="mailto:support@cybersmartempire.com">support@cybersmartempire.com</a></li>
                <li>Documentation: <a href="https://cybersmartempire.com/docs/" target="_blank">View Docs</a></li>
                <li>Professional Help:
                    <a href="https://cybersmartempire.com/consultation/" target="_blank">
                        Request Assistance
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <script>
    const btn = document.getElementById('csd_install_btn');
    if (btn) {
        btn.addEventListener('click', () => {
            const bar = document.getElementById('csd_bar');
            document.getElementById('csd_loader').style.display = 'block';
            let p = 0;

            const i = setInterval(() => {
                p = Math.min(p + 10, 90);
                bar.style.width = p + '%';
            }, 300);

            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=csd_fetch_security&nonce=<?php echo esc_js( $nonce ); ?>'
            })
            .then(r => r.json())
            .then(d => {
                clearInterval(i);
                bar.style.width = '100%';
                if (d.success) {
                    window.open(d.data.installer_url, '_blank');
                    setTimeout(() => location.reload(), 1500);
                }
            });
        });
    }
    </script>

<?php
}
