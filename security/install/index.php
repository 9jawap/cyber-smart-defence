<?php
include "core.php";
head();

if (isset($_POST['database_host'])) {
    $_SESSION['database_host'] = addslashes($_POST['database_host']);
} else {
    $_SESSION['database_host'] = '';
}
if (isset($_POST['database_username'])) {
    $_SESSION['database_username'] = addslashes($_POST['database_username']);
} else {
    $_SESSION['database_username'] = '';
}
if (isset($_POST['database_password'])) {
    $_SESSION['database_password'] = addslashes($_POST['database_password']);
} else {
    $_SESSION['database_password'] = '';
}
if (isset($_POST['database_name'])) {
    $_SESSION['database_name'] = addslashes($_POST['database_name']);
} else {
    $_SESSION['database_name'] = '';
}
?>

<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="fas fa-database fa-3x text-success mb-3"></i>
                <h2 class="fw-bold text-white">Database Configuration</h2>
<p class="text-light mb-4">Enter your database connection details. If you’re not sure, contact your hosting provider.</p>

            </div>

            <form method="post" action="" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Database Host</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-server text-success"></i></span>
                        <input type="text" name="database_host" class="form-control" placeholder="localhost" value="<?php echo $_SESSION['database_host']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Database Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-list-alt text-success"></i></span>
                        <input type="text" name="database_name" class="form-control" placeholder="security" value="<?php echo $_SESSION['database_name']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Database Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user text-success"></i></span>
                        <input type="text" name="database_username" class="form-control" placeholder="root" value="<?php echo $_SESSION['database_username']; ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Database Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-key text-success"></i></span>
                        <input type="text" name="database_password" class="form-control" placeholder="(optional)" value="<?php echo $_SESSION['database_password']; ?>">
                    </div>
                </div>

                <?php
                if (isset($_POST['submit'])) {
                    $database_host     = $_POST['database_host'];
                    $database_name     = $_POST['database_name'];
                    $database_username = $_POST['database_username'];
                    $database_password = $_POST['database_password'];
                    
                    @$db = mysqli_connect($database_host, $database_username, $database_password, $database_name);
                    if (!$db) {
                        echo '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error establishing a database connection.</div>';
                    } else {
                        echo '<meta http-equiv="refresh" content="0; url=settings.php" />';
                    }
                }
                ?>

                <button class="btn btn-success btn-lg col-12 shadow-sm" type="submit" name="submit">
                    <i class="fas fa-arrow-circle-right"></i> Next
                </button>
            </form>

            <hr class="my-4">

            <div class="system-checks">
                <?php
                // PHP Sessions check
                $_SESSION['phpsess_check'] = "Test";
                if (!isset($_SESSION['phpsess_check'])) {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> PHP Sessions are not enabled.</div>';
                } else {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> PHP Sessions are active.</div>';
                }

                // PHP MySQLi check
                if(!function_exists('mysqli_connect')) {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> PHP MySQLi extension is not enabled.</div>';
                } else {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> MySQLi extension is enabled.</div>';
                }

                // PHP cURL check
                if (!extension_loaded('curl')) {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> PHP cURL extension is not enabled.</div>';
                } else {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> cURL extension is enabled.</div>';
                }

                // PHP json check
                if (!function_exists('json_decode')) {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> PHP json_decode function is not enabled.</div>';
                } else {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> JSON functions are available.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
footer();
?>
