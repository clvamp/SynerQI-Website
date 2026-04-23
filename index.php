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
            </ul>
        </nav>
        
        <button class="cta-button" id="ctaButton" onclick="openGeneralModal()">Book Appointment</button>
        
        <div class="hamburger-menu" id="hamburgerMenu">
            <span class="material-icons-round" id="hamburgerIcon">menu</span>
        </div>
    </header>

    <main>
        <section class="hero" id="home">
            <div class="hero-overlay-gradient"></div>
            <div class="hero-content fade-in">
                <div class="hero-text-box">
                    <p class="subtitle">Restore Balance • Relieve Pain • Renew Energy</p>
                    <h1>SynerQi Traditional Chinese<br>Medicine Clinic</h1>
                    <p class="hero-subtext">Feel better the natural way</p>
                    
                    <div class="hero-divider"></div>
                    
                    <div class="hero-actions">
                        <button class="btn btn-primary pulse-anim" onclick="openGeneralModal()">Book Appointment</button>
                        <a href="services.php" class="btn btn-secondary">Explore Treatments</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="quick-nav-container">
            <nav class="quick-nav">
                <a href="#values">Values</a>
                <a href="#philosophy">Philosophy</a>
                <a href="#treatments">Treatments</a>
            </nav>
        </div>

        <section class="values-section fade-in" id="values">
            <div class="container">
                <div class="section-header-minimal">
                    <h2>The SynerQi Standard</h2>
                    <p>Holistic health care based on wealth of experience.</p>
                </div>
                
                <div class="values-grid">
                    <div class="value-item">
                        <span class="material-icons-round value-icon">spa</span>
                        <h3>Safe, Natural Healing</h3>
                        <p>Gentle therapies that activate your body's self-healing abilities without harmful side effects.</p>
                    </div>

                    <div class="v-divider"></div>

                    <div class="value-item">
                        <span class="material-icons-round value-icon">psychology</span>
                        <h3>Holistic Care</h3>
                        <p>We treat the root cause of stress, pain, and imbalance to restore your mind and body harmony.</p>
                    </div>

                    <div class="v-divider"></div>

                    <div class="value-item">
                        <span class="material-icons-round value-icon">verified_user</span>
                        <h3>Professional Experts</h3>
                        <p>Care provided exclusively by experienced TCM physicians and structural specialists.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="split-section fade-in" id="philosophy">
            <div class="split-container">
                <div class="split-image">
                    <img src="images/tcm-850x567 (1).jpg" alt="Dr. Treating Patient">
                    <div class="floating-badge">
                        <span class="material-icons-round">favorite</span>
                        <p>Patient-Centered Care</p>
                    </div>
                </div>
                <div class="split-content">
                    <h4>Our Philosophy</h4>
                    <h2>Caring for your health</h2>
                    <p>We believe in a holistic, safe approach in natural healing and preventive medicine, based on established practice.</p>
                    <p>Our team of professional TCM physicians and Certified Acupuncturists integrate Eastern and Western methodologies to provide effective treatment.</p>
                    <a href="about.html" class="text-link">Learn more about our credibility →</a>
                </div>
            </div>
        </section>

        <section class="featured-services fade-in" id="treatments">
            <div class="container">
                <div class="section-header-row">
                    <h2>Treatments</h2>
                    <a href="services.html" class="view-all-link">View All Treatments →</a>
                </div>
                
                <div class="featured-grid">
                    <div class="featured-card highlight-border" onclick="openGeneralModal()">
                        <div class="tag-corner">Popular</div>
                        <div class="f-image-wrap">
                            <img src="images/tcm-850x567 (1).jpg" alt="Consultation">
                            <div class="f-overlay"><span>Book Now</span></div>
                        </div>
                        <div class="featured-content">
                            <h3>Consultation</h3>
                            <p class="benefit-sub">Diagnosis & Planning</p>
                            <div class="meta-row">
                                <span class="f-price">₱1,000</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="featured-card" onclick="openGeneralModal()">
                        <div class="f-image-wrap">
                            <img src="images/Acupuncture-850x567.jpeg" alt="Acupuncture">
                            <div class="f-overlay"><span>Book Now</span></div>
                        </div>
                        <div class="featured-content">
                            <h3>Acupuncture</h3>
                            <p class="benefit-sub">Relieve chronic pain & restore energy</p>
                            <div class="meta-row">
                                <span class="f-price">₱1,500</span>
                            </div>
                        </div>
                    </div>

                    <div class="featured-card" onclick="openGeneralModal()">
                        <div class="f-image-wrap">
                            <img src="images/Cupping-850x567.jpg" alt="Cupping">
                            <div class="f-overlay"><span>Book Now</span></div>
                        </div>
                        <div class="featured-content">
                            <h3>Cupping Therapy</h3>
                            <p class="benefit-sub">Detoxify & improve circulation</p>
                            <div class="meta-row">
                                <span class="f-price">₱1,000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

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
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="services.html">Treatments</a></li>
                    <li><a href="contact.html">Contact</a></li>
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
                    <a href="contact.html" style="text-decoration: underline; color: #008080;">See full contact details →</a>
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
    
    <div id="bookingModal" class="modal-overlay">
        <div class="modal-wrapper">
            <div class="mobile-modal-header">
                <h4>Complete Request</h4>
                <span class="material-icons-round close-btn-mobile" onclick="closeModal()">close</span>
            </div>
            <div class="modal-grid">
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
                
                <div class="modal-form-section">
                    <div class="form-header-desktop">
                        <h3>Complete Request</h3>
                        <span class="material-icons-round close-btn-desktop" onclick="closeModal()">close</span>
                    </div>
                    <form id="modalBookingForm" action="process_booking.php" method="POST">
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

                        <div id="legalModal" class="legal-overlay">
                            <div class="legal-popup">
                                <div class="legal-header">
                                    <h3>Terms & Services</h3>
                                    <span class="material-icons-round close-legal" onclick="closeLegalModal()">close</span>
                                </div>
                                <div class="legal-body">
                                    <h4>1. Medical Disclaimer</h4>
                                    <p>Information from SynerQi is for general guidance only and does not replace professional medical advice. Always consult our certified physicians for diagnosis or treatment.</p>
                                    
                                    <h4>2. Data Privacy</h4>
                                    <p>Your personal and health information is kept secure and handled according to health privacy regulations.</p>
                                    
                                    <h4>3. Cancellation</h4>
                                    <p>Please provide accurate information when booking and give at least 24 hours notice if you need to cancel or reschedule.</p>
                                
                                    <h4>4. Patient Conduct</h4>
                                    <p>We maintain a respectful and safe clinical environment. Service may be refused if behavior disrupts patient care or clinic operations.</p>
                                
                                    <button type="button" class="terms-trigger-btn nav-style-button" onclick="window.location.href='about.php#terms'">
                                        See Full Details
                                    </button>
                                </div>
                                <button type="button" class="legal-confirm-btn" onclick="closeLegalModal()">I Understand</button>
                            </div>
                        </div>
                        
                        <button type="submit" class="submit-request-btn">
                            Confirm Request
                        </button>
                        <input type="hidden" name="totalAmount" id="totalAmountInput" value="0">
                        <input type="hidden" name="selectedServicesList" id="servicesListInput" value="">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="script/bookapp.js" type="text/javascript"></script>
    <script src="script/navbar.js" type="text/javascript">></script>
</body>
</html>

