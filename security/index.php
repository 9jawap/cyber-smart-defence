<?php
$configfile = 'config.php';
if (!file_exists($configfile)) {
    echo '<meta http-equiv="refresh" content="0; url=install" />';
    exit();
}

include "config.php";

if(!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['sec-username'])) {
    $uname = $_SESSION['sec-username'];
    if ($uname == $settings['username']) {
        echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
        exit;
    }
}

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

$error = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CYBER-SMART EMPIRE - Cyber Defence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/img/favicon.png">

    <style>
        body {
            background: radial-gradient(circle at center, #0a0a0a 0%, #000000 100%);
            color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.3px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: linear-gradient(145deg, #111111, #1a1a1a);
            border: 1px solid #222;
            border-radius: 25px;
            box-shadow: 0 0 25px rgba(255, 204, 0, 0.25);
            padding: 50px 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        .login-container h1 {
            color: #ffcc00;
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .login-container p {
            color: #cfcfcf;
            font-size: 0.95rem;
            margin-bottom: 25px;
        }

        .login-container input {
            width: 100%;
            background: #0f0f0f;
            border: 1px solid #333;
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            border-color: #ffcc00;
            box-shadow: 0 0 8px rgba(255, 204, 0, 0.3);
            outline: none;
        }

        .btn-login {
            background-color: #ffcc00;
            border: none;
            color: #000;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-login:hover {
            background-color: #e6b800;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(255, 204, 0, 0.25);
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.1);
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .brand-footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #888;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Floating Home Button */
        .home-btn {
            position: fixed;
            top: 50%;
            left: 25px;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            background: #00ff00;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.6);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .home-btn:hover {
            background: #00cc00;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 0 25px rgba(0, 255, 0, 0.9);
        }

        .home-btn svg {
            width: 28px;
            height: 28px;
            stroke: #000;
        }
    </style>
</head>
<body>

<!-- Floating Home Button -->
<a href="https://cybersmartempire.com" class="home-btn" title="Go to Homepage">
  <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z" />
  </svg>
</a>

<div class="login-container">
    <h1>Cyber Defence</h1>
    <p>Authorized personnel only — CYBER-SMART EMPIRE Security Gateway</p>

    <?php
    if (isset($_POST['signin'])) {
        $ip = addslashes(htmlentities($_SERVER['REMOTE_ADDR']));
        if ($ip == "::1") $ip = "127.0.0.1";
        $date = date("d F Y");
        $time = date("H:i");

        $username = mysqli_real_escape_string($mysqli, $_POST['username']);
        $password = hash('sha256', $_POST['password']);

        if ($username == $settings['username'] && $password == $settings['password']) {
            $checklh = $mysqli->query("SELECT id FROM `psec_logins` WHERE `username`='$username' AND ip='$ip' AND date='$date' AND time='$time' AND successful='1'");
            if (mysqli_num_rows($checklh) == 0) {
                $mysqli->query("INSERT INTO `psec_logins` (username, ip, date, time, successful) VALUES ('$username', '$ip', '$date', '$time', '1')");
            }
            $_SESSION['sec-username'] = $username;
            echo '<meta http-equiv="refresh" content="0;url=dashboard.php">';
        } else {
            $mysqli->query("INSERT INTO `psec_logins` (username, ip, date, time, successful) VALUES ('$username', '$ip', '$date', '$time', '0')");
            echo '<div class="alert-danger"><i class="fas fa-exclamation-circle"></i> Invalid <strong>Username</strong> or <strong>Password</strong>.</div>';
            $error = 1;
        }
    }
    ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" <?php if ($error == 1) echo 'autofocus'; ?> required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="signin" class="btn-login"><i class="fas fa-lock"></i> Login Securely</button>
    </form>

    <div class="brand-footer">
        Protected by CYBER-SMART EMPIRE
    </div>
</div>

</body>
</html>
