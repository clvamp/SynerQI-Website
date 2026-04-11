<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatments - SynerQi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="design/style.css">
    <link rel="stylesheet" href="design/services.css">
    <link rel="stylesheet" href="design/bookapp.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/synerqi header logo.png" alt="SynerQi Logo">
            </a>
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
            <span class="material-icons-round">menu</span>
        </div>
    </header>

    <main>
        <section class="service-header">
            <h1>Our Treatments</h1>
            <p>Effective, natural healing tailored to you.</p>
        </section>

        <div class="control-bar-wrapper">
            <div class="control-bar" id="controlBar">
                <div class="search-container">
                    <span class="material-icons-round search-icon">search</span>
                    <input type="text" id="serviceSearch" placeholder="Search symptoms, treatments..." aria-label="Search services">
                </div>
                
                <div class="filter-tabs" role="tablist">
                    <button class="filter-btn active" data-filter="all" role="tab" tabindex="0">All</button>
                    <button class="filter-btn" data-filter="medical" role="tab" tabindex="0">Medical</button>
                    <button class="filter-btn" data-filter="modalities" role="tab" tabindex="0">TCM Modalities</button>
                    <button class="filter-btn" data-filter="rehab" role="tab" tabindex="0">Rehabilitation</button>
                    <button class="filter-btn" data-filter="pain" role="tab" tabindex="0">Pain Management</button>
                    <button class="filter-btn filter-reset" id="resetFilters" title="Clear all filters" style="display:none; color: #d32f2f; border-color: #d32f2f;">
                        <span class="material-icons-round" style="font-size: 14px; vertical-align: middle;">close</span> Reset
                    </button>
                </div>
            </div>
            
            <div id="filterStatus" class="filter-feedback">
                Currently viewing: <strong>All Services</strong>
            </div>
        </div>

        <div class="services-container">
            <div class="service-card fade-in-item" data-category="medical">
                <div class="card-image"><img src="images/tcm-850x567 (1).jpg" alt="Consultation" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">medical_services</span> Consultation</h3>
                            <span class="price">₱1,000</span>
                        </div>
                        <ul class="bullets"><li>Pulse</li><li>Diagnose</li></ul>
                        <button class="btn-book" onclick="openModal('Consultation', '₱1,000', 'images/tcm-850x567 (1).jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Traditional Chinese Medicine TCM Diagnose involves Observation, Hearing & Smelling, Inquiring and Pulsing Methods.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="medical">
                <div class="card-image"><img src="images/Acupuncture-850x567.jpeg" alt="Acupuncture" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">healing</span> Acupuncture</h3>
                            <span class="price">₱1,500+</span>
                        </div>
                        <ul class="bullets"><li>Promotes Meridian Channel Flow</li><li>Balance QI of the Body</li><li>Replenish QI of the Body</li></ul>
                        <button class="btn-book" onclick="openModal('Acupuncture', '₱1,500+', 'images/Acupuncture-850x567.jpeg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Acupuncture promotes Meridian channel flow, balance qi and replenish qi of the body through the natural way, its effect has been recognized by WHO. Best for acute and chronic pain, also great remedy for internal medicines.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="medical">
                <div class="card-image"><img src="images/Herbal-Remedies-850x567 (1).jpg" alt="Herbal Remedies" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">local_florist</span> Herbal Remedies</h3>
                            <span class="price">₱200 - ₱300 /day</span>
                        </div>
                        <ul class="bullets"><li>Prescription Herbs</li></ul>
                        <button class="btn-book" onclick="openModal('Herbal Remedies', '₱200 - ₱300 /day', 'images/Herbal-Remedies-850x567 (1).jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Prescription of Herbs (Powder/Capsules). Traditional Chinese medicine prescription of herbs (powder/capsules) are customized to individual. Easy & convenient to prepare and consume. With GMP certified standard.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="modalities">
                <div class="card-image"><img src="images/Cupping-850x567.jpg" alt="Cupping" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">radio_button_unchecked</span> Cupping</h3>
                            <span class="price">₱1,000</span>
                        </div>
                        <ul class="bullets"><li>Detox</li><li>Slimming</li><li>Pain</li></ul>
                        <button class="btn-book" onclick="openModal('Cupping', '₱1,000', 'images/Cupping-850x567.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Traditional Chinese medicine way of Detox / Slimming / Pain Management. Relaxing taut muscles by expelling wind and heat of the body. Promotes circulation and reduce pain.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="modalities">
                <div class="card-image"><img src="images/tuina.jpg" alt="Tuina" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">front_hand</span> Tuina</h3>
                            <span class="price">₱1,000 / ₱1,500</span>
                        </div>
                        <ul class="bullets"><li>De-stress Muscles</li><li>Meridian Acupressure Points Massage</li></ul>
                        <button class="btn-book" onclick="openModal('Tuina', '₱1,000 / ₱1,500', 'images/tuina.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Meridian Acupressure Points Massage is the most effective way to de-stress the muscles and mind, thus promotes better sleep! Acupuncture points are being stimulated which helps qi flows better, thus bring better circulation of blood and strengthen mind and body.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="modalities">
                <div class="card-image"><img src="images/guasha.jpg" alt="Guasha" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">spa</span> Guasha</h3>
                            <span class="price">₱1,200</span>
                        </div>
                        <ul class="bullets"><li>Body Detox</li><li>Facial Lifting</li></ul>
                        <button class="btn-book" onclick="openModal('Guasha', '₱1,200', 'images/guasha.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Body Detox / Facial Lifting Guasha. Traditional Chinese medicine Body Detox Guasha is for detoxing and promoting lymphatic drainage. Facial lifting Guasha is able to detox and promote elasticity of face muscles, create lifting effect, reduce wrinkles and enhance radiance.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="rehab">
                <div class="card-image"><img src="images/rehab.jpeg" alt="Rehabilitation" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">accessibility</span> Rehabilitation</h3>
                            <span class="price">₱1,000</span>
                        </div>
                        <ul class="bullets"><li>Active Approach</li><li>Progressive Exercise</li></ul>
                        <button class="btn-book" onclick="openModal('Rehabilitation', '₱1,000', 'images/rehab.jpeg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Our Active Approach incorporates tailored, progressive exercise programs, to patient management sets us apart and enhances positive outcomes for our patients.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="rehab">
                <div class="card-image"><img src="images/3d_acu.jpg" alt="3D Acu" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">3d_rotation</span> 3D Acu & Myofascial Pain Management</h3>
                            <span class="price">₱4,000 - ₱7,500</span>
                        </div>
                        <ul class="bullets"><li>Structural rehabilitation</li><li>Manual Therapies</li><li>Restore Mobility</li></ul>
                        <button class="btn-book" onclick="openModal('3D Acu & Myofascial Pain Management', '₱4,000 - ₱7,500', 'images/3d_acu.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Dr Kim Ki Ho from Korea is a specialist in 3D structure Myofascial Chain Rehab therapy, this includes the combination of acupuncture and manual therapies remedies to restore, improve or maintain the movement and functioning of the human body for better mobility, coordination and pain management.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="rehab">
                <div class="card-image"><img src="images/physical_exercise.jpg" alt="Physical Exercise" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">fitness_center</span> Physical Exercise</h3>
                            <span class="price">₱600 - ₱800</span>
                        </div>
                        <ul class="bullets"><li>Restore Function</li><li>Muscle Injury Recovery</li><li>Stroke Recovery</li></ul>
                        <button class="btn-book" onclick="openModal('Physical Exercise', '₱600 - ₱800', 'images/physical_exercise.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Our Physical Exercise Therapy helps to restore the optimal function to people with injuries to the muscles, bones, tissues or nervous system, including those with stroke to enhance and restore their functional ability and quality of life.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="pain">
                <div class="card-image"><img src="images/3d_acu.jpg" alt="3D Acu Pain" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">sick</span> 3D Acu & Myofascial Pain Management</h3>
                            <span class="price">₱4,000 - ₱7,500</span>
                        </div>
                        <ul class="bullets"><li>Structural rehabilitation</li><li>Manual Therapies</li><li>Restore Mobility</li></ul>
                        <button class="btn-book" onclick="openModal('3D Acu & Myofascial Pain Management', '₱4,000 - ₱7,500', 'images/3d_acu.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Specialized Pain Management using 3D structure Myofascial Chain Rehab therapy. Combines acupuncture and manual therapies to restore movement and functioning of the human body.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="pain">
                <div class="card-image"><img src="images/Acupuncture-850x567.jpeg" alt="Acupuncture Pain" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">healing</span> Acupuncture</h3>
                            <span class="price">₱1,500+</span>
                        </div>
                        <ul class="bullets"><li>Promotes Meridian Channel Flow</li><li>Balance QI of the Body</li><li>Replenish QI of the Body</li></ul>
                        <button class="btn-book" onclick="openModal('Acupuncture', '₱1,500+', 'images/Acupuncture-850x567.jpeg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">The efficacy of Acupuncture treatment can provide pain relief but also the improvement of blood circulation for achieving overall balance of your physical condition.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

            <div class="service-card fade-in-item" data-category="pain">
                <div class="card-image"><img src="images/guasha.jpg" alt="Cupping" loading="lazy" onerror="this.src='images/tcm-850x567 (1).jpg'"></div>
                <div class="card-content-wrapper">
                    <button class="info-toggle" title="View Details" aria-label="View Service Details" aria-expanded="false" role="button"><span class="material-icons-round">info</span></button>
                    <div class="content-slide slide-summary active">
                        <div class="header-group">
                            <h3 class="service-title"><span class="material-icons-round title-icon">accessibility_new</span> Cupping / Guasha</h3>
                            <span class="price">₱1,000 / ₱1,200</span>
                        </div>
                        <ul class="bullets"><li>Resolve Pain Symptoms</li><li>Free Up Mobility</li><li>Treat Inflammation</li></ul>
                        <button class="btn-book" onclick="openModal('Cupping/Guasha', '₱1,000 / ₱1,200', 'images/guasha.jpg')" role="button">Book Now</button>
                    </div>
                    <div class="content-slide slide-detail">
                        <div class="header-group"><h3>About This Service</h3></div>
                        <p class="detail-text">Guasha can be effective for resolving pain symptoms and freeing up mobility for frozen shoulder. It can also be use to treat emerging inflammatory condition such as flulike symptoms or bronchial and respiratory illnesses.</p>
                        <button class="btn-back" aria-label="Go back to service summary" role="button"><span class="material-icons-round">arrow_back</span> Back</button>
                    </div>
                </div>
            </div>

        </div>

        <div id="noResults" style="display:none; text-align:center; padding: 3rem; color: #666;">
            <p>No treatments found. Try a different category.</p>
        </div>
    </main>

    <footer id="contact" class="fade-in">
        <div class="footer-content">
            <div class="footer-column">
                <div class="logo">
                    <img src="images/synerqi footer logo.png" alt="SynerQi Logo">
                </div>
                <p class="footer-desc">Restoring balance through the fusion of Eastern and Western medicine.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/synerqi#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
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
                <h4>Book Appointment</h4>
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
                        <span class="material-icons-round close-icon-desktop" onclick="closeModal()">close</span>
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
                                    <button type="button" class="legal-close-icon" onclick="closeLegalModal()">close</button>
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
                                
                                    <div class="full-terms-wrapper">
                                        <a href="about.php#terms" class="full-terms-link">See Full Details</a>
                                    </div>
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
    <script>
        // Scroll To Top 
        const scrollToTopBtn = document.getElementById("scrollToTopBtn");

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.style.display = "flex";
            } else {
                scrollToTopBtn.style.display = "none";
            }
        });

        scrollToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        document.getElementById('hamburgerMenu').onclick = function() {
            document.getElementById('mainNav').classList.toggle('open');
            document.getElementById('ctaButton').classList.toggle('open');
        };

        const fadeElements = document.querySelectorAll('.fade-in');
        const fadeInOnScroll = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (elementTop < windowHeight - 80) {
                    element.classList.add('visible');
                }
            });
        };
        window.addEventListener('scroll', fadeInOnScroll);
        fadeInOnScroll(); 

        const filterBtns = document.querySelectorAll('.filter-btn:not(#resetFilters)');
        const resetBtn = document.getElementById('resetFilters');
        const searchInput = document.getElementById('serviceSearch');
        const cards = document.querySelectorAll('.service-card');
        const noResults = document.getElementById('noResults');
        const filterStatus = document.getElementById('filterStatus');

        function updateFeedback() {
            const activeBtn = document.querySelector('.filter-btn.active');
            const category = activeBtn.getAttribute('data-filter');
            const term = searchInput.value.toLowerCase();
            let catText = category === 'all' ? 'All Services' : category.charAt(0).toUpperCase() + category.slice(1);
            let searchText = term ? ` + keyword "${term}"` : '';
            filterStatus.innerHTML = `Currently viewing: <strong>${catText}${searchText}</strong>`;
            
            if(category !== 'all' || term !== '') {
                resetBtn.style.display = 'inline-flex';
            } else {
                resetBtn.style.display = 'none';
            }
        }

        function filterItems() {
            const category = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            const term = searchInput.value.toLowerCase();
            let visibleCount = 0;
            cards.forEach(card => {
                const itemCat = card.getAttribute('data-category');
                
                const title = card.querySelector('.service-title')?.innerText.toLowerCase() || '';
                const bullets = card.querySelector('.bullets')?.innerText.toLowerCase() || '';
                const details = card.querySelector('.detail-text')?.innerText.toLowerCase() || '';
                
                const text = title + " " + bullets + " " + details;

                const matchesCategory = (category === 'all' || itemCat === category);
                const matchesSearch = text.includes(term);
                
                if (matchesCategory && matchesSearch) {
                    card.style.display = 'flex';
                    card.classList.remove('fade-in-item');
                    void card.offsetWidth; 
                    card.classList.add('fade-in-item');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            updateFeedback();
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                filterItems();
            });
        });
        
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterBtns.forEach(b => b.classList.remove('active'));
            document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
            filterItems();
        });

        searchInput.addEventListener('input', filterItems);

        filterBtns.forEach(btn => {
            btn.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') btn.click();
            });
        });

        document.querySelectorAll('.service-card').forEach(card => {
            const toggleBtn = card.querySelector('.info-toggle');
            const backBtn = card.querySelector('.btn-back');
            const summarySlide = card.querySelector('.slide-summary');
            const detailSlide = card.querySelector('.slide-detail');

            function toggleSlides() {
                const isSummaryActive = summarySlide.classList.contains('active');
                
                if (isSummaryActive) {
                    summarySlide.classList.remove('active');
                    detailSlide.classList.add('active');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.style.opacity = '0'; toggleBtn.style.pointerEvents = 'none';
                } else {
                    detailSlide.classList.remove('active');
                    summarySlide.classList.add('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.style.opacity = '1'; toggleBtn.style.pointerEvents = 'auto';
                }
            }
            toggleBtn.addEventListener('click', toggleSlides);
            backBtn.addEventListener('click', toggleSlides);
        });

        const modal = document.getElementById('bookingModal');
        const modalImg = document.getElementById('modalImg');
        const modalTitle = document.getElementById('modalTitle');
        const modalPrice = document.getElementById('modalPrice');
        const serviceSelect = document.getElementById('serviceSelect'); 
        const dateInput = document.getElementById('bookingDate');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        function getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;
        }

        function updateModalContext(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            modalTitle.innerText = selectedOption.value;
            modalPrice.innerText = selectedOption.getAttribute('data-price');
            modalImg.src = selectedOption.getAttribute('data-img');
        }
        window.updateModalContext = updateModalContext;

        window.openModal = function(title, price, imgSrc) {
            const scrollbarWidth = getScrollbarWidth();
            document.body.style.paddingRight = `${scrollbarWidth}px`;

            for(let i = 0; i < serviceSelect.options.length; i++) {
                if (serviceSelect.options[i].value === title) {
                    serviceSelect.selectedIndex = i;
                    break;
                }
            }
            
            updateModalContext(serviceSelect);

            modal.style.display = 'flex';
            setTimeout(() => { modal.classList.add('active'); }, 10);
            document.body.style.overflow = 'hidden';
        }

        window.openGeneralModal = function() {
             serviceSelect.selectedIndex = 0; 
             window.openModal('Consultation', '₱1,000', 'images/tcm-850x567 (1).jpg');
        }

        window.closeModal = function() {
            modal.classList.remove('active');
            setTimeout(() => { 
                modal.style.display = 'none'; 
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0px';
            }, 300);
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.getElementById('modalBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const service = document.getElementById('serviceSelect').value;
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const phone = document.getElementById('userPhone').value;
            const date = document.getElementById('bookingDate').value;
            const time = document.getElementById('bookingTime').value;
            const subject = `Booking Request: ${service} - ${name}`;
            const body = `Hello SynerQi,%0D%0A%0D%0AI would like to book an appointment.%0D%0A%0D%0ADetails:%0D%0AService: ${service}%0D%0AName: ${name}%0D%0AEmail: ${email}%0D%0APhone: ${phone}%0D%0ADate: ${date}%0D%0ATime: ${time}`;
            window.location.href = `mailto:emantablizo520@gmail.com?subject=${subject}&body=${body}`;
            closeModal();
        });
        
        // filter 
        filterItems();
    </script>
</body>
</html>
