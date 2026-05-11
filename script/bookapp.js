window.openGeneralModal = function(serviceName) {
    if (window.openGeneralModalShared) {
        window.openGeneralModalShared(serviceName);
    }
};

window.openGeneralModalShared = function(serviceName) {
    // Initialized as a placeholder until the page scripts are ready.
};

document.addEventListener('DOMContentLoaded', function() {

    // --- 1. DATE LIMITER ---
    const dateInput = document.getElementById('bookingDate');
    if (dateInput) {
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);
        
        const offset = today.getTimezoneOffset() * 60000;
        const localToday = new Date(today - offset).toISOString().split('T')[0];
        const localNextWeek = new Date(nextWeek - offset).toISOString().split('T')[0];
        
        dateInput.setAttribute('min', localToday);
        dateInput.setAttribute('max', localNextWeek);
    }

    // --- DROPDOWN TOGGLE FOR TREATMENTS ---
    const trigger = document.getElementById('serviceToggleButton');
    const dropdown = document.getElementById('servicesSelectionOverlay');
    const wrapper = document.querySelector('.custom-select-wrapper');

    if (trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();

            dropdown.classList.toggle('active');
            wrapper.classList.toggle('is-open');
        });
    }
    

// --- Updated UI Logic ---
    function updateAllDisplays() {
        let total = 0;
        let selectedNames = [];

        document.querySelectorAll('.service-check:checked').forEach(checkedBox => {
            total += parseInt(checkedBox.getAttribute('data-price')) || 0;
            selectedNames.push(checkedBox.value);
        });

        const triggerText = document.getElementById('selectedServicesText');
        if (triggerText) {
            if (selectedNames.length > 0) {
                triggerText.innerText = selectedNames.join(", ");
                triggerText.style.color = "#008080";
            } else {
                triggerText.innerText = "Select Treatments";
                triggerText.style.color = "#333";
            }
        }

        const cartTotalElement = document.getElementById('cartTotal');
        if (cartTotalElement) cartTotalElement.innerText = "₱" + total.toLocaleString();

        const listDisplay = document.getElementById('servicesList');
        if (listDisplay) {
            if (selectedNames.length > 0) {
                listDisplay.innerHTML = selectedNames.map(name => `<li>${name}</li>`).join("");
            } else {
                listDisplay.innerHTML = '<li class="no-services">No services selected</li>';
            }
        }

        const totalInput = document.getElementById('totalAmountInput');
        const servicesInput = document.getElementById('servicesListInput');
        if (totalInput) totalInput.value = total;
        if (servicesInput) servicesInput.value = selectedNames.join(", ");
    }

    window.updateAllDisplays = updateAllDisplays;

    // --- FORM DATA PERSISTENCE ---
    async function saveFormData() {
        const formData = {};

        // Save text inputs
        const inputs = document.querySelectorAll('#modalBookingForm input[type="text"], #modalBookingForm input[type="email"], #modalBookingForm input[type="tel"], #modalBookingForm input[type="date"]');
        inputs.forEach(input => {
            formData[input.name] = input.value;
        });

        // Save select
        const selects = document.querySelectorAll('#modalBookingForm select');
        selects.forEach(select => {
            formData[select.name] = select.value;
        });

        // Save textareas
        const textareas = document.querySelectorAll('#modalBookingForm textarea');
        textareas.forEach(textarea => {
            formData[textarea.name] = textarea.value;
        });

        // Save checkboxes (services)
        const checkedServices = [];
        document.querySelectorAll('.service-check:checked').forEach(checkbox => {
            checkedServices.push(checkbox.value);
        });
        formData.checkedServices = checkedServices;

        // Save terms checkbox
        const termsCheck = document.getElementById('termsCheck');
        if (termsCheck) {
            formData.terms_agreed = termsCheck.checked;
        }

        try {
            await fetch('save_form_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });
        } catch (error) {
            console.error('Error saving form data:', error);
        }
    }

    async function loadFormData() {
        try {
            const response = await fetch('save_form_data.php');
            const formData = await response.json();

            // Load text inputs
            Object.keys(formData).forEach(key => {
                if (key === 'checkedServices' || key === 'terms_agreed') return;
                const input = document.querySelector(`#modalBookingForm [name="${key}"]`);
                if (input) {
                    input.value = formData[key];
                }
            });

            // Load services checkboxes
            if (formData.checkedServices) {
                document.querySelectorAll('.service-check').forEach(checkbox => {
                    checkbox.checked = formData.checkedServices.includes(checkbox.value);
                });
            }

            // Load terms checkbox
            if (formData.terms_agreed !== undefined) {
                const termsCheck = document.getElementById('termsCheck');
                if (termsCheck) {
                    termsCheck.checked = formData.terms_agreed;
                }
            }

            // Update displays after loading
            updateAllDisplays();
        } catch (error) {
            console.error('Error loading form data:', error);
        }
    }

    // Add event listeners to save data on change
    document.addEventListener('input', saveFormData);
    document.addEventListener('change', saveFormData);

    // Logic to close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('servicesSelectionOverlay');
        const trigger = document.getElementById('serviceToggleButton');
        const wrapper = document.querySelector('.custom-select-wrapper');

        if (trigger && dropdown && wrapper && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            wrapper.classList.remove('is-open');
        }
    });

    const checkboxes = document.querySelectorAll('.service-check');
    checkboxes.forEach(box => {
        box.addEventListener('change', updateAllDisplays);
    });

    updateAllDisplays();

    const bookingModal = document.getElementById('bookingModal');
    if (bookingModal) {
        bookingModal.addEventListener('click', function(e) {
            if (e.target === bookingModal) {
                window.closeModal();
            }
        });
    }

    window.selectServiceInModal = function(serviceName) {
        if (!serviceName) return;
        const serviceCheckbox = document.querySelector(`.service-check[value="${serviceName}"]`);
        if (serviceCheckbox) {
            serviceCheckbox.checked = true;
            updateAllDisplays();
        }
    };

    window.openGeneralModalShared = function(serviceName) {
        const modal = document.getElementById('bookingModal');
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.paddingRight = `${scrollbarWidth}px`;

        if (serviceName) {
            window.selectServiceInModal(serviceName);
        }

        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
            document.body.style.overflow = 'hidden';
        }

        // Load saved form data
        loadFormData();
    };

    window.closeModal = function() {
        const modal = document.getElementById('bookingModal');
        if (!modal) return;
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0px';
        }, 300);
    };

    window.openGeneralModal = function(serviceName) {
        if (window.openGeneralModalShared) {
            window.openGeneralModalShared(serviceName);
        }
    };

    // Auto-open CTA modal when arriving from another page with the query string
    const urlParams = new URLSearchParams(window.location.search);
    const autoOpen = urlParams.get('openModal') || urlParams.get('open');
    if (autoOpen === '1' || autoOpen === 'true' || autoOpen === 'cta') {
        if (window.location.hash) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        window.openGeneralModal();
        history.replaceState(null, '', window.location.pathname + window.location.hash);
    }

// --- Terms & Services Modal Logic ---
window.openLegalModal = function() {
    const legalModal = document.getElementById('legalModal');
    if (legalModal) {
        legalModal.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }
};

window.closeLegalModal = function() {
    const legalModal = document.getElementById('legalModal');
    if (legalModal) {
        legalModal.classList.remove('active');
        
        // Re-enable scroll only if the main booking modal is also closed
        const bookingModal = document.getElementById('bookingModal');
        if (!bookingModal || !bookingModal.classList.contains('active')) {
            document.body.style.overflow = 'auto';
        }
    }
};

// Close when clicking the dark area
window.addEventListener('click', function(e) {
    const legalModal = document.getElementById('legalModal');
    if (e.target === legalModal) {
        closeLegalModal();
    }
});

// Final Validation on Submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modalBookingForm');
    const check = document.getElementById('termsCheck');
});

// CTA
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("modalBookingForm");
    const noticeModal = document.getElementById("validationNoticeModal");
    const noticeConfirmBtn = document.getElementById("noticeConfirmBtn");
    const noticeMessage = document.getElementById("noticeMessage");

    if (form) {
        // Disables the default HTML5 validation tooltips completely
        form.setAttribute("novalidate", "");

        form.addEventListener("submit", function (e) {
            // Pause submission to run our custom checks
            e.preventDefault(); 

            let missingField = null;
            let warningText = "";

            // 1. CHECK TREATMENTS (Custom Check)
            const checkedTreatments = document.querySelectorAll('.service-check:checked');
            if (checkedTreatments.length === 0) {
                missingField = document.getElementById("serviceToggleButton");
                warningText = "Please select at least one treatment before proceeding.";
            }

            // 2. CHECK STANDARD REQUIRED FIELDS (Name, Email, Date, Terms, etc.)
            // We only check fields that are currently visible to avoid getting stuck on hidden mobile/desktop elements
            if (!missingField) {
                const requiredElements = form.querySelectorAll('input[required]');
                for (let el of requiredElements) {
                    if (el.offsetWidth > 0 && el.offsetHeight > 0) { // If visible
                        if ((el.type === 'checkbox' && !el.checked) || (!el.value.trim())) {
                            missingField = el;
                            if (el.id === "termsCheck") {
                                warningText = "Please agree to the Terms and Services.";
                            } else {
                                warningText = "Please fill out all required contact and schedule fields.";
                            }
                            break; // Stop at the first empty field
                        }
                    }
                }
            }

            // 3. CHECK VITALS AND CONCERNS
            if (!missingField) {
                // Your CSS breakpoint for desktop is 1024px
                const isDesktop = window.innerWidth > 1024; 
                
                let vitalsField, concernField;
                
                if (isDesktop) {
                    vitalsField = document.getElementById("patientVitals");
                    concernField = document.getElementById("patientConcern");
                } else {
                    // Grabs the mobile-specific textareas based on your HTML
                    vitalsField = document.querySelector('.mobile-only textarea[name="patient_vitals"]');
                    concernField = document.querySelector('.mobile-only textarea[name="patient_concern"]');
                }

                if (vitalsField && !vitalsField.value.trim()) {
                    missingField = vitalsField;
                    warningText = "Please fill in your Patient Vitals (e.g., Blood Pressure, Heart Rate).";
                } else if (concernField && !concernField.value.trim()) {
                    missingField = concernField;
                    warningText = "Please describe your Patient Concerns or Symptoms.";
                }
            }

            // 4. FINAL ACTION: SHOW MODAL OR SUBMIT
            if (missingField) {
                // Show custom popup
                noticeMessage.textContent = warningText;
                noticeModal.classList.add("active");

                noticeConfirmBtn.onclick = function () {
                    // Close custom popup
                    noticeModal.classList.remove("active");
                    
                    // Smoothly scroll to the missing field and highlight it
                    setTimeout(() => {
                        // If the missing element is the terms checkbox, scroll to its container instead
                        let targetToScroll = missingField;
                        if (missingField.id === "termsCheck") {
                            const container = missingField.closest('.terms-container');
                            if (container) {
                                targetToScroll = container;
                                // Add a visual alert highlight class
                                container.classList.add("validation-highlight");
                                // Remove the highlight class after 2 seconds
                                setTimeout(() => {
                                    container.classList.remove("validation-highlight");
                                }, 2000);
                            }
                        }

                        targetToScroll.scrollIntoView({ behavior: "smooth", block: "center" });
                        
                        if (typeof missingField.focus === 'function') {
                            missingField.focus();
                        }
                    }, 250); 
                };
            } else {
                // Everything is valid! Submit the form normally to PHP.
                form.submit();
            }
        });
    }
});
});
