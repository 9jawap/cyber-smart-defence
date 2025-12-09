<?php
include "header.php";

$query = $mysqli->query("SELECT * FROM `psec_pages-layolt` WHERE page='Spam'");
$row   = mysqli_fetch_array($query);
?>

<!-- CYBER-SMART EMPIRE Spam Page -->
<style>
    body {
        background: radial-gradient(circle at center, #0a0a0a 0%, #000000 100%);
        color: #f5f5f5;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.3px;
    }
    .spam-container {
        min-height: 85vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 20px;
    }
    .spam-card {
        background: linear-gradient(145deg, #111111, #1a1a1a);
        border: 1px solid #222;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(255, 204, 0, 0.2);
        max-width: 700px;
        width: 100%;
        padding: 40px 30px;
        animation: fadeIn 1.2s ease-out;
    }
    .spam-card h1 {
        font-size: 2rem;
        color: #ffcc00;
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .spam-card p {
        color: #cfcfcf;
        font-size: 1rem;
        margin: 15px 0;
    }
    .spam-icon {
        font-size: 4.5rem;
        color: #ffcc00;
        margin: 25px 0;
        animation: glowPulse 2s infinite;
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
        margin-top: 10px;
    }
    .btn-luxury:hover {
        background-color: #e6b800;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(255, 204, 0, 0.2);
    }
    .btn-check {
        background-color: transparent;
        border: 2px solid #ffcc00;
        color: #ffcc00;
        font-weight: 500;
        padding: 10px 22px;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
    }
    .btn-check:hover {
        background-color: #ffcc00;
        color: #000;
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
    @keyframes glowPulse {
        0%, 100% {text-shadow: 0 0 5px #ffcc00, 0 0 10px #ffcc00;}
        50% {text-shadow: 0 0 20px #ffcc00, 0 0 30px #ffcc00;}
    }
</style>

<div class="spam-container">
    <div class="spam-card">
        <h1>Spam Activity Detected</h1>
        <div class="spam-icon"><i class="fas fa-keyboard"></i></div>
        <p><?php echo html_entity_decode($row['text']); ?></p>
        <p>Your IP or activity may have triggered our spam filters.  
        If you believe this was an error, please contact our webmaster for review.</p>

        <a href="mailto:<?php echo $settings['email']; ?>" class="btn-luxury"><i class="fas fa-envelope"></i> Contact Support</a>

        <p style="margin-top:25px; color:#ccc;">You can also verify if your IP appears on public spam databases:</p>
        <a href="https://www.dnsbl.info/dnsbl-database-check.php" class="btn-check" target="_blank">
            <i class="fas fa-search"></i> Check DNSBL Database
        </a>

        <div class="brand-footer">
            © <?php echo date('Y'); ?> CYBER-SMART EMPIRE — Digital Integrity First
        </div>
    </div>
</div>

<?php
include "footer.php";
?>
