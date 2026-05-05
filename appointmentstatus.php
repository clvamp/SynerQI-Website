<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SynerQi</title>
    <link rel="stylesheet" href="design/style.css">
    <link rel="stylesheet" href="design/homepage.css">
    <link rel="stylesheet" href="design/bookapp.css">
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

</body>
</html>
