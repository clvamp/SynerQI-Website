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

    // Update the trigger box text
    const triggerText = document.getElementById('selectedServicesText');
    if (selectedNames.length > 0) {
        triggerText.innerText = selectedNames.join(", ");
        triggerText.style.color = "#008080"; // Change color when selected
    } else {
        triggerText.innerText = "Select Treatments";
        triggerText.style.color = "#333";
    }

    // Update your existing price/hidden inputs logic below...
    const formattedTotal = "₱" + total.toLocaleString();
    if (document.getElementById('cartTotal')) cartTotal.innerText = formattedTotal;
    if (document.getElementById('totalAmountInput')) totalAmountInput.value = total;
}

// Logic to close dropdown when clicking outside
window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('servicesSelectionOverlay');
    const trigger = document.getElementById('serviceToggleButton');
    const wrapper = document.querySelector('.custom-select-wrapper');

    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
        wrapper.classList.remove('is-open');
    }
});

    // --- 3. CONSOLIDATED CALCULATION LOGIC ---
    const checkboxes = document.querySelectorAll('.service-check');
    
    // UI Display elements
    const cartTotal = document.getElementById('cartTotal');
    const selectedList = document.getElementById('selectedList');
    const totalPaymentDisplay = document.getElementById('modalTotalPayment');
    const servicesListDisplay = document.getElementById('modalServicesList');

    // HIDDEN INPUTS (These send data to process_booking.php)
    const totalAmountInput = document.getElementById('totalAmountInput');
    const servicesListInput = document.getElementById('servicesListInput');

    // --- Updated UI Logic ---
function updateAllDisplays() {
    let total = 0;
    let selectedNames = [];

    // 1. Gather data
    document.querySelectorAll('.service-check:checked').forEach(checkedBox => {
        total += parseInt(checkedBox.getAttribute('data-price')) || 0;
        selectedNames.push(checkedBox.value);
    });

    // 2. Update Total Payment
    const cartTotal = document.getElementById('cartTotal');
    if (cartTotal) cartTotal.innerText = "₱" + total.toLocaleString();

    // 3. Conditional Formatting Logic
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        const commaDisplay = document.getElementById('servicesComma');
        if (commaDisplay) {
            commaDisplay.innerText = selectedNames.length > 0 ? selectedNames.join(", ") : "No services selected";
        }
    } else {
        const listDisplay = document.getElementById('servicesList');
        const detailsElement = document.getElementById('servicesDropdown');
        
        if (listDisplay) {
            if (selectedNames.length > 0) {
                listDisplay.innerHTML = selectedNames.map(name => `<li>${name}</li>`).join("");
                if (detailsElement) detailsElement.setAttribute('open', ''); // Keep shown
            } else {
                listDisplay.innerHTML = '<li class="no-services">No services selected</li>';
            }
        }
    }

    // 4. Update hidden inputs for PHP form submission
    const totalInput = document.getElementById('totalAmountInput');
    const servicesInput = document.getElementById('servicesListInput');
    if (totalInput) totalInput.value = total;
    if (servicesInput) servicesInput.value = selectedNames.join(", ");
}
    checkboxes.forEach(box => {
        box.addEventListener('change', updateAllDisplays);
    });

    updateAllDisplays();
});

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
