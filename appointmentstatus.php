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
                    <li><a href="appointmentstatus.php">Status</a></li>
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

    <!-- CTA BUTTON POP UP FORM -->
    <div id="bookingModal" class="modal-overlay">
        <div class="modal-wrapper">
            <div class="mobile-modal-header">
                <h4>Complete Request</h4>
                <span class="material-icons-round close-btn-mobile" onclick="closeModal()">close</span>
            </div>

            <div class="modal-grid">

                <!-- LEFT -->
                <div class="modal-context-info">
                    <div class="info-label">Current Total</div>
                    <p class="info-value" id="cartTotal">₱0</p>

                    <div class="info-divider"></div>

                    <div class="info-label">Chosen Treatments</div>
                    <p id="servicesComma" class="info-value-small mobile-only">No services selected</p>
                    <div class="desktop-only">
                        <details id="servicesDropdown" open>
                            <summary>Selected List</summary>
                            <ul id="servicesList">
                                <li class="no-services">No services selected</li>
                            </ul>
                        </details>
                    </div>
                </div>

                <!-- CENTER (FORM) -->
                <div class="modal-form-section">
                    <div class="form-header-desktop">
                        <h3>Complete Request</h3>
                        <span class="material-icons-round close-btn-desktop" onclick="closeModal()">close</span>
                    </div>

                    <form id="modalBookingForm" action="database/process_booking.php" method="POST">

                        <div class="form-group">
                            <label>Treatments</label>
                            <div class="custom-select-wrapper">
                                <div class="select-box-trigger" id="serviceToggleButton">
                                    <span id="selectedServicesText">Select Treatments</span>
                                    <span class="material-icons-round">expand_more</span>
                                </div>

                                <div id="servicesSelectionOverlay" class="services-dropdown-content">
                                    <div class="checkbox-list">
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Consultation" data-price="1000">
                                            <span>Consultation - ₱1,000</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Acupuncture" data-price="1500">
                                            <span>Acupuncture - ₱1,500</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Herbal Remedies" data-price="300">
                                            <span>Herbal Remedies - ₱300</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Cupping" data-price="1200">
                                            <span>Cupping - ₱1,200</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Tuina" data-price="1000">
                                            <span>Tuina - ₱1,000</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Guasha" data-price="1200">
                                            <span>Guasha - ₱1,200</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Rehabilitation" data-price="1000">
                                            <span>Rehabilitation - ₱1,000</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="Physical Exercise" data-price="800">
                                            <span>Physical Exercise - ₱800</span>
                                        </label>
                                        <label class="check-item">
                                            <input type="checkbox" class="service-check" value="3D Acu & Myofascial" data-price="4000">
                                            <span>3D Acu & Myofascial - ₱4,000</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="userName" placeholder="Enter your name" required name="userName">
                        </div>

                        <!-- Mobile only -->
                        <div class="mobile-only">
                            <div class="form-group">
                                <label>Patient Vitals</label>
                                <textarea name="patient_vitals_mob" placeholder="BP, Temp, etc..." rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Patient Concern</label>
                                <textarea name="patient_concern_mob" placeholder="Symptoms..." rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="userEmail" placeholder="email@example.com" required name="userEmail">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" id="userPhone" placeholder="0912 345 6789" required name="userPhone">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="bookingDate" required name="date">
                            </div>
                            <div class="form-group">
                                <label>Time</label>
                                <select id="bookingTime" name="bookingTime">
                                    <option>Morning (9AM - 12PM)</option>
                                    <option>Afternoon (1PM - 5PM)</option>
                                </select>
                            </div>
                        </div>

                        <div class="terms-container">
                            <div class="terms-row">
                                <label class="terms-check-wrapper">
                                    <input type="checkbox" id="termsCheck" name="terms_agreed" required>
                                    <span class="terms-text">I have read the</span>
                                </label>
                                <button type="button" class="terms-trigger-btn" onclick="openLegalModal()">
                                    Terms and Services
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="submit-request-btn">
                            Confirm Request
                        </button>

                        <input type="hidden" name="totalAmount" id="totalAmountInput" value="0">
                        <input type="hidden" name="selectedServicesList" id="servicesListInput" value="">
                    </form>
                </div>

                <!-- RIGHT (CORRECT POSITION NOW) -->
                <div class="modal-patient-notes desktop-only">

                    <div class="notes-group">
                        <label>Patient Vitals</label>
                        <textarea 
                            name="patient_vitals"
                            id="patientVitals"
                            form="modalBookingForm"
                            placeholder="Blood Pressure, Heart Rate, Temperature..."
                            required
                        ></textarea>
                    </div>

                    <div class="notes-group">
                        <label>Patient Concern</label>
                        <textarea 
                            name="patient_concern"
                            id="patientConcern"
                            form="modalBookingForm"
                            placeholder="Describe symptoms, pain, concerns..."
                            required
                        ></textarea>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="script/bookapp.js" type="text/javascript"></script>
    <script src="script/navbar.js" type="text/javascript"></script>

</body>
</html>
