<?php
/**
 * Plugin Name: Cyber Smart Defence
 * Plugin URI: https://cybersmartempire.com/cyberdefence/
 * Description: Free enterprise-grade website protection by Cyber Smart Empire. Users can install manually, via one-click, or request professional setup.
 * Version: 1.8
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
// AJAX HANDLER: FETCH & EXTRACT SECURITY ZIP
// -----------------------------------------------------------------------------
add_action('wp_ajax_csd_fetch_security', 'csd_fetch_security');
function csd_fetch_security() {
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
    WP_Filesystem();
    global $wp_filesystem;

    $zip_url = 'https://cybersmartempire.com/enviroment/security.zip';
    $destination = ABSPATH . 'security/';

    // Download remote zip
    $tmp_file = download_url($zip_url);
    if (is_wp_error($tmp_file)) wp_send_json_error('Failed to download package.');

    // Ensure destination exists
    if (!$wp_filesystem->is_dir($destination)) $wp_filesystem->mkdir($destination);

    // Extract
    $archive = new PclZip($tmp_file);
    if ($archive->extract(PCLZIP_OPT_PATH, $destination) == 0) {
        unlink($tmp_file);
        wp_send_json_error('Failed to extract security package: ' . $archive->errorInfo(true));
    }

    // Cleanup temp file
    unlink($tmp_file);

    // Success
    $installer_url = site_url('/security/');
    wp_send_json_success(['installer_url' => $installer_url]);
}

// -----------------------------------------------------------------------------
// ADMIN NOTICES
// -----------------------------------------------------------------------------
function csd_admin_notices() {
    if (csd_security_system_ready()) {
        echo '<div class="notice notice-success is-dismissible">
                <p><strong>🛡 Cyber Smart Defence is ACTIVE.</strong> Your website is now protected.</p>
              </div>';
    } else {
        echo '<div class="notice notice-warning">
                <p><strong>⚠ Cyber Smart Defence engine not yet installed.</strong></p>
                <p>You can install it automatically from the plugin page or request professional setup.</p>
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
// ADMIN PAGE
// -----------------------------------------------------------------------------
function csd_admin_page() {
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
        <?php else: ?>
            <h2>One-Click Automatic Installation</h2>
            <button id="csd_install_btn" class="button button-primary">Install Security Engine to Root & Run Installer</button>
            <div id="csd_progress" style="margin-top:10px;width:100%;background:#eee;height:20px;display:none;">
                <div style="width:0%;height:100%;background:#4caf50;" id="csd_progress_bar"></div>
            </div>
            <p id="csd_fallback" style="display:none;">
                <a href="<?php echo esc_url($security_panel_url); ?>" target="_blank" class="button button-secondary">
                    Install the Security Engine Manually
                </a>
            </p>
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

    <script>
    document.getElementById('csd_install_btn')?.addEventListener('click', function() {
        var progress = document.getElementById('csd_progress');
        var bar = document.getElementById('csd_progress_bar');
        var fallback = document.getElementById('csd_fallback');
        progress.style.display = 'block';
        bar.style.width = '0%';

        var interval = setInterval(function(){
            var width = parseInt(bar.style.width);
            if(width >= 90) clearInterval(interval);
            bar.style.width = (width+10) + '%';
        }, 500);

        fetch(ajaxurl + '?action=csd_fetch_security&_wpnonce=<?php echo wp_create_nonce("csd_install_engine"); ?>', {method: 'POST'})
        .then(res => res.json())
        .then(data => {
            clearInterval(interval);
            bar.style.width = '100%';
            if(data.success) {
                window.open(data.data.installer_url, '_blank');
                location.reload();
            } else {
                alert('Installation failed: ' + data.data);
                fallback.style.display = 'block';
            }
        }).catch(err=>{
            clearInterval(interval);
            alert('Installation failed: '+err);
            fallback.style.display = 'block';
        });
    });
    </script>
    <?php
}
