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

    if (form) {
        form.addEventListener('submit', function(e) {
            if (check && !check.checked) {
                e.preventDefault();
                alert("Please agree to the Terms and Services to continue.");
            }
        });
    }
});