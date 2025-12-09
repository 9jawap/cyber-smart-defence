<?php
include "header.php";
?>

<!-- CYBER-SMART EMPIRE Fake Bot Page -->
<style>
    body {
        background: radial-gradient(circle at center, #0a0a0a 0%, #000000 100%);
        color: #f5f5f5;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.3px;
    }
    .bot-container {
        min-height: 85vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 20px;
    }
    .bot-card {
        background: linear-gradient(145deg, #111111, #1a1a1a);
        border: 1px solid #222;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(255, 204, 0, 0.2);
        max-width: 650px;
        width: 100%;
        padding: 40px 30px;
        animation: fadeIn 1.2s ease-out;
    }
    .bot-card h1 {
        font-size: 2rem;
        color: #ffcc00;
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .bot-card p {
        color: #cfcfcf;
        font-size: 1rem;
        margin: 15px 0;
    }
    .bot-icon {
        font-size: 4.5rem;
        color: #ffcc00;
        margin: 25px 0 15px;
        position: relative;
        display: inline-block;
        animation: pulseIcon 2s infinite;
    }
    .bot-icon .fa-user-secret {
        color: #ffcc00;
    }
    .bot-icon .fa-ban {
        position: absolute;
        top: 0;
        left: 0;
        color: #ff4d4d;
        font-size: 4.5rem;
        opacity: 0.85;
    }
    .btn-luxury {
        background-color: #ffcc00;
        border: none;
        color: #000;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-luxury:hover {
        background-color: #e6b800;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(255, 204, 0, 0.2);
    }
    .brand-footer {
        margin-top: 40px;
        font-size: 0.9rem;
        color: #888;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(20px);}
        to {opacity: 1; transform: translateY(0);}
    }
    @keyframes pulseIcon {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.85; }
    }
</style>

<div class="bot-container">
    <div class="bot-card">
        <div class="bot-icon">
            <i class="fas fa-user-secret"></i>
            <i class="fas fa-ban"></i>
        </div>

        <h1>Fake Bot Detected</h1>

        <p>You were identified as a non-genuine or automated bot attempting to access this site.</p>
        <p>For security reasons, your connection has been blocked to protect our system from malicious activity.</p>
        <p>If you believe this action was made in error, please reach out to the administrator.</p>

        <a href="mailto:<?php echo $settings['email']; ?>" class="btn-luxury">
            <i class="fas fa-envelope"></i> Contact Support
        </a>

        <div class="brand-footer">
            © <?php echo date('Y'); ?> CYBER-SMART EMPIRE — Security Beyond Limits
        </div>
    </div>
</div>

<?php
include "footer.php";
?>
