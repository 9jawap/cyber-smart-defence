<?php
/**
 * Plugin Name: Cyber Smart Defence
 * Plugin URI: https://cybersmartempire.com/cyberdefence/
 * Description: Free enterprise-grade website protection by Cyber Smart Empire. Users can install manually or request professional setup.
 * Version: 1.6
 * Author: Cyber Smart Empire
 * Author URI: https://cybersmartempire.com
 * License: GPLv3
 * Text Domain: cyber-smart-defence
 */

if (!defined('ABSPATH')) exit;

// -----------------------------------------------------------------------------
// CHECK IF ROOT SECURITY ENGINE EXISTS
// -----------------------------------------------------------------------------
function csd_security_system_ready() {
    $security_path = ABSPATH . 'security/';
    return is_dir($security_path) &&
           file_exists($security_path . 'config.php') &&
           file_exists($security_path . 'project-security.php');
}

// -----------------------------------------------------------------------------
// AUTO-INTEGRATE SECURITY ENGINE (FRONTEND ONLY)
// -----------------------------------------------------------------------------
function csd_init_security_integration() {
    if (is_admin()) return;
    if (csd_security_system_ready()) {
        include_once ABSPATH . 'security/config.php';
        include_once ABSPATH . 'security/project-security.php';
    }
}
add_action('init', 'csd_init_security_integration', 1);

// -----------------------------------------------------------------------------
// INSTALL SECURITY ENGINE TO ROOT (USER CLICK) & REDIRECT TO INSTALLER
// -----------------------------------------------------------------------------
function csd_install_security_engine() {

    if (!current_user_can('manage_options')) return;
    check_admin_referer('csd_install_engine');

    $source = plugin_dir_path(__FILE__) . 'security/';
    $destination = ABSPATH . 'security/';

    if (!is_dir($source)) {
        wp_die('Security engine package is missing inside the plugin.');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();

    global $wp_filesystem;

    if (!$wp_filesystem->is_dir($destination)) {
        $wp_filesystem->mkdir($destination);
    }

    copy_dir($source, $destination);

    // Redirect user to the first-time installer
    $installer_url = site_url('/security/');
    echo '<script>window.open("' . esc_url($installer_url) . '", "_blank");</script>';

    // Redirect back to plugin page after a brief delay (optional)
    echo '<meta http-equiv="refresh" content="0; url=' . esc_url(admin_url('admin.php?page=cyber-smart-defence&installed=1')) . '">';
    exit;
}

// -----------------------------------------------------------------------------
// ADMIN NOTICES
// -----------------------------------------------------------------------------
function csd_admin_notices() {
    if (isset($_GET['installed'])) {
        echo '<div class="notice notice-success is-dismissible">
            <p><strong>✅ Cyber Smart Defence Engine Installed Successfully.</strong> Installer opened in a new tab.</p>
        </div>';
    }

    if (csd_security_system_ready()) {
        echo '<div class="notice notice-success is-dismissible">
                <p><strong>🛡 Cyber Smart Defence is ACTIVE.</strong> Your website is now protected.</p>
              </div>';
    } else {
        echo '<div class="notice notice-warning">
                <p><strong>⚠ Cyber Smart Defence engine not yet installed.</strong></p>
                <p>You may install it automatically or request a professional setup.</p>
              </div>';
    }
}
add_action('admin_notices', 'csd_admin_notices');

// -----------------------------------------------------------------------------
// ADMIN MENU
// -----------------------------------------------------------------------------
function csd_add_admin_menu() {
    add_menu_page(
        'Cyber Defence',
        'Cyber Defence',
        'manage_options',
        'cyber-smart-defence',
        'csd_admin_page',
        'dashicons-shield-alt',
        80
    );
}
add_action('admin_menu', 'csd_add_admin_menu');

// -----------------------------------------------------------------------------
// ADMIN DASHBOARD PAGE
// -----------------------------------------------------------------------------
function csd_admin_page() {

    if (isset($_POST['csd_install_engine'])) {
        csd_install_security_engine();
    }

    $is_ready = csd_security_system_ready();
    $status = $is_ready
        ? '<span style="color:green;font-weight:bold;">Active</span>'
        : '<span style="color:red;font-weight:bold;">Not Installed</span>';

    $security_panel_url = site_url('/security/');
    ?>

    <div class="wrap">
        <h1>🛡 Cyber Smart Defence</h1>
        <p><strong>Status:</strong> <?php echo $status; ?></p>

        <?php if ($is_ready): ?>
            <div style="margin-top:15px;">
                <a href="<?php echo esc_url($security_panel_url); ?>" 
                   class="button button-primary" 
                   target="_blank">
                    Open Cyber Defence Panel
                </a>
            </div>
        <?php endif; ?>

        <hr>

        <?php if (!$is_ready): ?>
        <h2>One-Click Automatic Installation</h2>
        <form method="post">
            <?php wp_nonce_field('csd_install_engine'); ?>
            <button type="submit" name="csd_install_engine" class="button button-primary">
                Install Security Engine to Root & Run Installer
            </button>
        </form>
        <?php endif; ?>

        <hr>

        <h2>For Manual Installation</h2>
        <ol>
            <li>Copy the <strong>security</strong> folder to your site root.</li>
            <li>Visit <code>yourdomain.com/security/</code> to run the installer.</li>
            <li>Reload your WordPress dashboard.</li>
        </ol>

        <hr>

        <h2>Professional Installation & Support (Optional)</h2>
        <ul>
            <li>Full secure setup</li>
            <li>Advanced server hardening</li>
            <li>Malware cleanup</li>
            <li>Emergency response</li>
            <li>Security monitoring</li>
        </ul>

        <p>
            <a href="https://cybersmartempire.com/cyberdefence" class="button button-primary" target="_blank">
                Request Professional Setup
            </a>
            <a href="mailto:support@cybersmartempire.com" class="button">
                Contact Support
            </a>
        </p>

        <hr>
        <p><a href="https://cybersmartempire.com" target="_blank">🌐 Visit Cyber Smart Empire</a></p>
    </div>
    <?php
}
