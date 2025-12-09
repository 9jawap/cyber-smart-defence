<?php
include "core.php";
include "../config_settings.php";
head();

$database_host     = $_SESSION['database_host'];
$database_username = $_SESSION['database_username'];
$database_password = $_SESSION['database_password'];
$database_name     = $_SESSION['database_name'];

if (isset($_SERVER['HTTPS'])) {
    $htp = 'https';
} else {
    $htp = 'http';
}
$settings['site_url']             = $htp . '://' . $_SERVER['SERVER_NAME'];
$fullpath                         = "$htp://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$settings['projectsecurity_path'] = substr($fullpath, 0, strpos($fullpath, '/install'));
$settings['sqli_redirect']        = $settings['projectsecurity_path'] . '/pages/blocked.php';
$settings['proxy_redirect']       = $settings['projectsecurity_path'] . '/pages/proxy.php';
$settings['spam_redirect']        = $settings['projectsecurity_path'] . '/pages/spammer.php';
$settings['username']             = $_SESSION['username'];
$settings['password']             = hash('sha256', $_SESSION['password']);

file_put_contents('../config_settings.php', '<?php $settings = ' . var_export($settings, true) . '; ?>');

@$db = new mysqli($database_host, $database_username, $database_password, $database_name);
if ($db) {
    $query = '';
    $sql_dump = file('database.sql');
    foreach ($sql_dump as $line) {
        $startWith = substr(trim($line), 0, 2);
        $endWith   = substr(trim($line), -1, 1);
        if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
            continue;
        }
        $query = $query . $line;
        if ($endWith == ';') {
            mysqli_query($db, $query) or die('Problem executing SQL query: <b>' . $query . '</b>');
            $query = '';
        }
    }

    // Write config
    $config_file = file_get_contents(CONFIG_FILE_TEMPLATE);
    $config_file = str_replace("<DB_HOST>", $database_host, $config_file);
    $config_file = str_replace("<DB_NAME>", $database_name, $config_file);
    $config_file = str_replace("<DB_USER>", $database_username, $config_file);
    $config_file = str_replace("<DB_PASSWORD>", $database_password, $config_file);
    @chmod(CONFIG_FILE_PATH, 0777);
    @$f = fopen(CONFIG_FILE_PATH, "w+");
    if (!fwrite($f, $config_file) > 0) {
        echo 'Cannot open the configuration file to save the information';
    }
    fclose($f);
} else {
    echo 'Error establishing a database connection. Please check your parameters.<br />';
}
?>

<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shield-alt fa-4x text-success"></i>
            </div>
            <h2 class="fw-bold mb-3 text-success">Installation Complete!</h2>
            <p class="lead mb-4 text-muted">Your <strong>Cyber-Smart Empire Security Protection</strong> is now successfully installed and active.</p>
            
            <div class="alert alert-success shadow-sm text-start mb-4">
                <h5 class="fw-bold text-success mb-2"><i class="fas fa-check-circle"></i> Status: Installed Successfully</h5>
                <p class="mb-0">The core protection engine is ready. You can now activate full protection on your website.</p>
            </div>

            <div class="alert alert-warning text-start shadow-sm mb-4">
                <h5 class="fw-bold text-warning mb-2"><i class="fas fa-exclamation-triangle"></i> Important</h5>
                <p class="mb-0">For security reasons, please <strong>delete or rename the <code>install/</code> folder</strong> from your server.</p>
            </div>

            <div class="text-start bg-light border rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fab fa-wordpress"></i> For WordPress Websites</h5>
                <p class="mb-2">This plugin automatically integrates the <code>/security/</code> folder protection engine.</p>
                <ol class="ps-3">
                    <li>Upload the <code>security</code> folder to your site root (same level as <code>wp-config.php</code>).</li>
                    <li>Ensure it contains <code>config.php</code> and <code>project-security.php</code>.</li>
                    <li>Return to your <strong>WordPress Dashboard</strong> and refresh the page to activate full protection.</li>
                </ol>
                <a href="../wp-admin" class="btn btn-primary mt-2"><i class="fas fa-sync"></i> Go to WordPress Dashboard</a>
            </div>

            <div class="text-start bg-light border rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-code"></i> For Non-WordPress Websites</h5>
                <p class="mb-3">To integrate manually, add the following code in your main PHP file (such as <code>index.php</code>, <code>header.php</code>, or <code>core.php</code>):</p>
                <div class="bg-dark text-light p-3 rounded small">
<pre><code>&lt;?php
// ------------------------------------------------------
// CYBER-SMART EMPIRE SECURITY PROTECTION (Active)
// ------------------------------------------------------

include_once __DIR__ . '/security/config.php';
include_once __DIR__ . '/security/project-security.php';
?&gt;
</code></pre>
                </div>
            </div>

            <a href="../" class="btn btn-success btn-lg px-5 shadow-sm">
                <i class="fas fa-arrow-circle-right"></i> Continue to Cyber Defence Panel
            </a>
        </div>
    </div>
</div>

<?php footer(); ?>
