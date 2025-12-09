<?php
include "core.php";
head();

// Initialize session variables
$_SESSION['username'] = $_POST['username'] ?? ($_SESSION['username'] ?? '');
$_SESSION['password'] = $_POST['password'] ?? ($_SESSION['password'] ?? '');
?>

<div class="container py-5">
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 600px; border-radius: 16px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-primary">Administrator Setup</h4>
                <p class="text-muted mb-0">Please provide the following information. You can always change these settings later.</p>
            </div>
            <hr class="my-4">

            <form method="post" action="">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="admin"
                            value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" class="form-control"
                            value="<?php echo htmlspecialchars($_SESSION['password']); ?>" required>
                    </div>
                </div>

                <?php
                if (isset($_POST['submit'])) {
                    $username = addslashes($_POST['username']);
                    $password = $_POST['password'];

                    echo '<meta http-equiv="refresh" content="0; url=done.php" />';
                }
                ?>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary px-4">
                        Next<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php footer(); ?>
