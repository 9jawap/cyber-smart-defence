<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
if(!isset($_SESSION)) {
    session_start();
}

// SETTINGS
define("CONFIG_FILE_DIRECTORY", "../");
define("CONFIG_FILE_NAME", "config.php");
define("CONFIG_FILE_PATH", CONFIG_FILE_DIRECTORY . CONFIG_FILE_NAME);
define("CONFIG_FILE_TEMPLATE", "config.tpl");

if (file_exists(CONFIG_FILE_PATH)) {
    echo '<meta http-equiv="refresh" content="0; url=../" />';
    exit;
}

function head() {
    $current_page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);  
    if($current_page == 'settings.php'){
        $page = 2; 
    } elseif ($current_page == 'done.php') {
        $page = 3;
    } else {
        $page = 1;
    }

    // Progress percentage
    $progress = ($page == 1) ? 33 : (($page == 2) ? 66 : 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>CYBER DEFENCE - Installation Wizard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="../assets/img/favicon.png">

    <!-- Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top left, #03140a 0%, #000000 80%);
            font-family: 'Poppins', sans-serif;
            color: #e9e9e9;
            min-height: 100vh;
            background-attachment: fixed;
        }

        .card {
            background: linear-gradient(145deg, rgba(40, 60, 40, 0.85), rgba(25, 40, 25, 0.85));
            border-radius: 20px;
            border: 1px solid rgba(0, 255, 150, 0.4);
            box-shadow: 0 0 40px rgba(0, 255, 150, 0.25), inset 0 0 20px rgba(0, 255, 150, 0.08);
            margin-top: 80px;
            backdrop-filter: blur(12px);
            animation: fadeInUp 0.8s ease;
            color: #ffffff;
        }

        .glow {
            text-shadow: 0 0 10px #00ff90, 0 0 20px #00ff90;
        }

        h3.title {
            color: #00ff90;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #cfcfcf;
            font-size: 15px;
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.9);
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #00ff90;
        }

        .nav-tabs .nav-link.active {
            color: #00ff90;
            border-bottom: 3px solid #00ff90;
            background: transparent;
            font-weight: 600;
        }

        .progress {
            height: 10px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            margin-top: 15px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #00ff90, #00b87a);
            width: 0%;
            transition: width 1s ease-in-out;
        }

        .content-area {
            padding: 2rem 1rem;
            color: #f2f2f2;
            font-size: 15px;
            line-height: 1.6;
        }

        .brand-icon {
            background: linear-gradient(135deg, #00ff90 0%, #007744 100%);
            color: #000;
            border-radius: 50%;
            padding: 14px;
            font-size: 22px;
            box-shadow: 0 0 25px rgba(0, 255, 150, 0.4);
        }

        footer {
            margin-top: 50px;
            color: #bbb;
            font-size: 14px;
            text-align: center;
        }

        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .step-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #ddd;
        }

        .tab-content {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card p-4">
            <div class="text-center mb-3">
                <i class="fas fa-shield-alt brand-icon"></i>
                <h3 class="title glow">CYBER DEFENCE</h3>
                <div class="subtitle">Installation Wizard</div>
            </div>

            <ul class="nav nav-tabs nav-fill">
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 1) echo 'active'; ?>">
                        <i class="fas fa-database me-1"></i> Database
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 2) echo 'active'; ?>">
                        <i class="fas fa-user-shield me-1"></i> Admin Setup
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 3) echo 'active'; ?>">
                        <i class="fas fa-check-circle me-1"></i> Complete
                    </a>
                </li>
            </ul>

            <div class="progress mt-3">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
            </div>
            <div class="step-label text-center mt-2">Step <?php echo $page; ?> of 3</div>

            <div class="content-area">
                <div class="tab-content" id="TabContent">
<?php
}

function footer() {
?>
                </div>
            </div>

            <footer>
                &copy; <?php echo date("Y"); ?> CyberSmart Empire. All Rights Reserved.
            </footer>
        </div>
    </div>

    <script>
        // Smooth progress animation
        document.addEventListener("DOMContentLoaded", function() {
            const bar = document.querySelector('.progress-bar');
            bar.style.width = bar.getAttribute('style').split(':')[1];
        });
    </script>
</body>
</html>
<?php
}
?>
