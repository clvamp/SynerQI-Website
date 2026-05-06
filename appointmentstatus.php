<?php
// 1. DATABASE CONNECTION
$conn = new mysqli('127.0.0.1', 'root', '', 'synerqi_db');

$search_result = null;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $raw_input = $_POST['reference_number'];
    
    // 2. EXTRACT THE NUMBER (e.g., "SYNERQI-0005" becomes "5")
    $search_id = (int)preg_replace('/[^0-9]/', '', $raw_input);

    if ($search_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM online_appointments WHERE id = ?");
        $stmt->bind_param("i", $search_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $search_result = $result->fetch_assoc();
        } else {
            $error_message = "No appointment found with that ID.";
        }
        $stmt->close();
    } else {
        $error_message = "Please enter a valid reference number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SynerQi</title>
    <link rel="stylesheet" href="design/style.css">
    <link rel="stylesheet" href="design/homepage.css">
    <link rel="stylesheet" href="design/bookapp.css">
    <link rel="stylesheet" href="design/Section.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">
            <img src="images/synerqi header logo.png" alt="SynerQi Logo">
        </div>
        
        <nav id="mainNav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="services.php">Treatments</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="appointmentstatus.php">Status</a></li>
            </ul>
        </nav>
        
        <button class="cta-button" id="ctaButton" onclick="openGeneralModal()">Book Appointment</button>
        
        <div class="hamburger-menu" id="hamburgerMenu">
            <span class="material-icons-round" id="hamburgerIcon">menu</span>
        </div>
    </header>

    <!--MAIN BODY: REFERAL DIV-->

    <section class="referral-section">
        <div class="referral-container">
            <h2>Track Request</h2>
            <p class="referral-subtext">
                Enter your reference number (e.g., SYNERQI-0001) to track your status.
            </p>

            <form method="POST" class="referral-box">
                <input type="text" name="reference_number" placeholder="Reference number" required 
                    value="<?php echo htmlspecialchars($_POST['reference_number'] ?? ''); ?>">

                <p class="referral-help">
                    Forgot your reference number? <a href="contact.php">Contact us</a>
                </p>

                <button type="submit" class="referral-btn">Check Status</button>
            </form>

            <!-- 3. DISPLAY SEARCH RESULTS -->
            <?php if ($search_result): ?>
                <div class="status-result-card" style="margin-top: 2rem; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <h3 style="color: #008080; margin-bottom: 10px;">Appointment Found</h3>
                    <p><strong>Patient:</strong> <?php echo htmlspecialchars($search_result['patient_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y - h:i A', strtotime($search_result['date'])); ?></p>
                    
                    <div style="margin-top: 15px; padding: 10px; border-radius: 8px; text-align: center; font-weight: bold; 
                        <?php echo ($search_result['status'] === 'Confirmed') ? 'background: #e6fffa; color: #008080;' : 'background: #fffaf0; color: #d97706;'; ?>">
                        Status: <?php echo htmlspecialchars($search_result['status']); ?>
                    </div>
                </div>
            <?php elseif ($error_message): ?>
                <p style="color: #e53e3e; margin-top: 1rem; font-weight: 500;"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!--ENDS HERE-->

    <footer id="contact" class="fade-in">
        <div class="footer-content">
            <div class="footer-column">
                <div class="logo">
                    <img src="images/synerqi footer logo.png" alt="SynerQi Logo">
                </div>
                <p class="footer-desc">Restoring balance through the fusion of Eastern and Western medicine.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Treatments</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Visit Us</h4>
                <p>
                    604 City and Land Megaplaza<br>
                    Ortigas Center, Pasig City
                </p>
                <p style="margin-top: 1rem;">
                    <a href="tel:+639950461097" style="color: white; font-weight: 600;">0995 046 1097</a>
                </p>
                <p>
                    <a href="contact.php" style="text-decoration: underline; color: #008080;">See full contact details →</a>
                </p>
            </div>

            <div class="footer-column">
                <h4>Clinic Hours</h4>
                <ul class="hours-list">
                    <li><span>Mon - Sun:</span> 8:00 AM - 7:00 PM</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            Copyright © 2025 So Earth – All Rights Reserved.
        </div>
    </footer>

    <button id="scrollToTopBtn" title="Go to top">
        <span class="material-icons-round">arrow_upward</span>
    </button>

    <script src="script/bookapp.js" type="text/javascript"></script>
    <script src="script/navbar.js" type="text/javascript"></script>

</body>
</html>
