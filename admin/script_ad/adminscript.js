// --- CALENDAR LOGIC ---
    function loadCalendar() {
        const dt = new Date();
        if (nav !== 0) {
            dt.setMonth(new Date().getMonth() + nav);
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0); 
        
        // Limiter
        const endLimit = new Date();
        endLimit.setDate(today.getDate() + 7); 
        endLimit.setHours(23, 59, 59, 999);

        const month = dt.getMonth();
        const year = dt.getFullYear();

        const firstDayOfMonth = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const paddingDays = firstDayOfMonth.getDay();

        monthYearDisplay.innerText = dt.toLocaleDateString('en-us', { month: 'long', year: 'numeric' });
        calendarDays.innerHTML = '';

        for(let i = 1; i <= paddingDays + daysInMonth; i++) {
            const daySquare = document.createElement('div');
            // Base styles for all active days
            daySquare.classList.add('py-2', 'border', 'border-gray-50', 'rounded-lg', 'min-h-[70px]', 'flex', 'flex-col', 'items-center', 'justify-start', 'transition');

            if (i > paddingDays) {
                const dayNum = i - paddingDays;
                const dateToCheck = new Date(year, month, dayNum);
                const dayString = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
                
                daySquare.innerText = dayNum;
                const dayEvents = calendarEvents[dayString];

                // ALLOW ACCESS: We no longer check if (dateToCheck >= today)
                // We only check the future limit if you still want to restrict bookings too far ahead
                const isTooFarInFuture = dateToCheck > endLimit;

                if (!isTooFarInFuture) {
                    // ENABLED STYLES (Past days and Next 7 days)
                    daySquare.classList.add('cursor-pointer', 'hover:border-primary', 'hover:shadow-sm', 'text-gray-800');
                    daySquare.onclick = () => showAppointments(dayString, dayEvents);

                    // Highlight days with appointments
                    if (dayEvents && dayEvents.length > 0) {
                        daySquare.classList.add('bg-teal-50', 'text-teal-700', 'font-medium');
                        const badge = document.createElement('div');
                        badge.classList.add('mt-1', 'bg-teal-500', 'text-white', 'text-[10px]', 'px-2', 'py-0.5', 'rounded-full');
                        badge.innerText = `${dayEvents.length} Appt${dayEvents.length > 1 ? 's' : ''}`;
                        daySquare.appendChild(badge);
                    }

                    // Highlight Today specifically
                    if (dateToCheck.getTime() === today.getTime()) {
                        daySquare.classList.add('ring-2', 'ring-primary', 'font-bold');
                    }
                } else {
                    // DISABLED STYLES (Only for dates far in the future)
                    daySquare.classList.add('bg-gray-100', 'text-gray-300', 'cursor-not-allowed', 'opacity-50');
                }

            } else {
                daySquare.classList.add('bg-gray-50', 'text-transparent', 'pointer-events-none');
            }

            calendarDays.appendChild(daySquare);
        }
    }

    function showAppointments(dateString, dayEvents) {
        const displayDt = new Date(dateString);
        selectedDateDisplay.innerText = `Scheduled for ${displayDt.toLocaleDateString('en-us', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}`;
        dateAppointmentsList.innerHTML = '';

        if (!dayEvents || dayEvents.length === 0) {
            dateAppointmentsList.innerHTML = '<p class="text-sm text-gray-400 italic text-center mt-4">No patients scheduled.</p>';
            return;
        }

        dayEvents.forEach(event => {
            const evDiv = document.createElement('div');
            evDiv.classList.add('p-3', 'bg-gray-50', 'border', 'border-gray-100', 'rounded-lg', 'flex', 'flex-col', 'gap-1');
            evDiv.innerHTML = `
                <div class="font-semibold text-gray-800 text-sm">${event.name}</div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="far fa-clock text-primary"></i> ${event.time}
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-user-md text-primary"></i> ${event.doctor}
                </div>
            `;
            dateAppointmentsList.appendChild(evDiv);
        });
    }

// --- MODAL & FORM LOGIC ---
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Patient ID    
function openAddPatientModal() {
    const displayField = document.getElementById('add_patient_id_display');
    const hiddenField = document.getElementById('add_db_id');
    
    let highestID = 0;
    const idCells = document.querySelectorAll('.id-cell');

    if (idCells.length === 0) {
        highestID = 0; // explicit fallback
    } else {
        idCells.forEach(cell => {
            const match = cell.textContent.trim().match(/SYNERQI-(\d+)/);
            if (match) {
                const num = parseInt(match[1], 10);
                if (num > highestID) highestID = num;
            }
        });
    }

    const nextID = highestID + 1;
    const formattedID = 'SYNERQI-' + String(nextID).padStart(4, '0');

    if (displayField) displayField.value = formattedID;
    if (hiddenField) hiddenField.value = nextID;

    openModal('addModal');
}

/**
 * Populates and opens the Edit Patient modal
 * Now includes the system-generated Patient ID display
 */
function editPatient(id, name, date, phone, doctor) {
    // 1. Update the visible (readonly) Patient ID field
    const idDisplay = document.getElementById('edit_patient_id_display');
    if (idDisplay) {
        idDisplay.value = id;
    }

    // 2. Update the hidden input for the database query
    const dbIdInput = document.getElementById('edit_db_id');
    if (dbIdInput) {
        dbIdInput.value = id;
    }

    // 3. Populate the remaining fields
    if (document.getElementById('edit_name')) document.getElementById('edit_name').value = name;
    if (document.getElementById('edit_date')) document.getElementById('edit_date').value = date;
    if (document.getElementById('edit_phone')) document.getElementById('edit_phone').value = phone;
    if (document.getElementById('edit_doctor')) document.getElementById('edit_doctor').value = doctor;
    
    openModal('editModal');
}

// Initialization for interactive elements
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Close Modals on Background Click ---
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            // Only close if the background (the overlay) was clicked, not the content box
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // --- Treatment Dropdown Logic ---
    const toggle = document.getElementById('serviceToggleButton');
    const overlay = document.getElementById('servicesSelectionOverlay');
    const wrapper = document.querySelector('.custom-select-wrapper');
    const checkboxes = document.querySelectorAll('.service-check');
    const textDisplay = document.getElementById('selectedServicesText');

    if (toggle && overlay) {
        // Toggle dropdown visibility
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.classList.toggle('active');
            wrapper.classList.toggle('is-open');
        });

        // Update display text when checkboxes change
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const selected = Array.from(checkboxes)
                    .filter(i => i.checked)
                    .map(i => i.value);
                
                textDisplay.innerText = selected.length > 0 ? selected.join(', ') : 'Select Treatments';
                
                // Style adjustment for placeholder vs active text
                if (selected.length > 0) {
                    textDisplay.classList.add('text-gray-900');
                    textDisplay.classList.remove('text-gray-500');
                } else {
                    textDisplay.classList.remove('text-gray-900');
                    textDisplay.classList.add('text-gray-500');
                }
            });
        });

        // Close dropdown when clicking anywhere outside of it
        document.addEventListener('click', (e) => {
            if (wrapper && !wrapper.contains(e.target)) {
                overlay.classList.remove('active');
                wrapper.classList.remove('is-open');
            }
        });
    }
});

// --- AUTO CALCULATE TOTAL AMOUNT BASED ON SELECTED SERVICES ---
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.service-check');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', calculateTotalAmount);
    });

    function calculateTotalAmount() {
        let total = 0;

        checkboxes.forEach(service => {
            if (service.checked) {
                total += parseInt(service.dataset.price) || 0;
            }
        });

        // Find or create hidden total amount field for PHP submission
        let hiddenTotalInput = document.getElementById('totalAmountInput');

        if (!hiddenTotalInput) {
            hiddenTotalInput = document.createElement('input');
            hiddenTotalInput.type = 'hidden';
            hiddenTotalInput.name = 'totalAmount';
            hiddenTotalInput.id = 'totalAmountInput';

            const form = document.querySelector('#addModal form');
            if (form) {
                form.appendChild(hiddenTotalInput);
            }
        }

        hiddenTotalInput.value = total;
    }
});

// date-limit.js
document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.querySelector('input[name="date"]');

    if (!dateInput) return;

    const now = new Date();

    // Current time = minimum selectable
    const minDate = formatDateTimeLocal(now);

    // Maximum selectable = 7 days from today, until 11:59 PM
    const endLimit = new Date();
    endLimit.setDate(now.getDate() + 7);
    endLimit.setHours(23, 59, 0, 0);

    const maxDate = formatDateTimeLocal(endLimit);

    dateInput.min = minDate;
    dateInput.max = maxDate;
});

function formatDateTimeLocal(date) {
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let day = String(date.getDate()).padStart(2, '0');
    let hours = String(date.getHours()).padStart(2, '0');
    let minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Error Notice Treatments Required
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector('#addModal form');
    const checks = document.querySelectorAll('.service-check');
    const errorText = document.getElementById("servicesError");
    const wrapper = document.querySelector(".custom-select-wrapper");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        const checked = document.querySelectorAll('.service-check:checked');

        if (checked.length === 0) {
            e.preventDefault();

            errorText.classList.remove("hidden");
            wrapper.classList.add("error");

            document.getElementById("serviceToggleButton").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        }
    });

    checks.forEach(box => {
        box.addEventListener("change", function () {

            const checked = document.querySelectorAll('.service-check:checked');

            if (checked.length > 0) {
                errorText.classList.add("hidden");
                wrapper.classList.remove("error");
            }
        });
    });

});


// --- REAL-TIME SEARCH FILTER LOGIC ---
            function filterTable() {
                let input = document.getElementById("searchInput");
                if (!input) return;
                
                let filter = input.value.toUpperCase();
                let tbody = document.getElementById("patientTableBody");
                let tr = tbody.getElementsByTagName("tr");

                for (let i = 0; i < tr.length; i++) {
                    if (tr[i].getElementsByTagName("td").length > 1) {
                        let tdID = tr[i].getElementsByTagName("td")[0];
                        let tdName = tr[i].getElementsByTagName("td")[1];
                        let tdPhone = tr[i].getElementsByTagName("td")[3];
                        
                        if (tdID || tdName || tdPhone) {
                            let textID = tdID.textContent || tdID.innerText;
                            let textName = tdName.textContent || tdName.innerText;
                            let textPhone = tdPhone.textContent || tdPhone.innerText;
                            
                            if (textID.toUpperCase().indexOf(filter) > -1 || 
                                textName.toUpperCase().indexOf(filter) > -1 || 
                                textPhone.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = ""; 
                            } else {
                                tr[i].style.display = "none"; 
                            }
                        }
                    }
                }
            }

function openViewModal(patient) {
    document.getElementById('view_db_id').value = patient.id;
    document.getElementById('view_name').innerText = patient.userName || patient.patient_name || 'Unknown';
    document.getElementById('view_phone').innerText = patient.userPhone || patient.telephone || 'N/A';
    document.getElementById('view_schedule').innerText = patient.date ? new Date(patient.date).toLocaleDateString() : 'N/A';
    document.getElementById('view_doctor').value = patient.assigned_doctor || "";
    
    openModal('viewPatientModal');
}

function showAppointments(dateString, dayEvents) {
    selectedDateDisplay.innerText = `Scheduled for ${dateString}`;
    dateAppointmentsList.innerHTML = '';

    if (!dayEvents || dayEvents.length === 0) {
        dateAppointmentsList.innerHTML = '<p class="text-sm text-gray-400 italic">No patients.</p>';
        return;
    }

    dayEvents.forEach(event => {
        const isConfirmed = event.is_confirmed == 1;
        const evDiv = document.createElement('div');
        
        // This adds the gold border if confirmed
        evDiv.className = `p-3 bg-white border rounded-lg flex flex-col gap-1 ${isConfirmed ? 'border-amber-400 ring-2 ring-amber-100' : 'border-gray-100'}`;
        
        evDiv.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="font-bold text-gray-800 text-sm">${event.name}</div>
                ${isConfirmed ? '<span class="text-[10px] text-amber-600 font-bold">CONFIRMED</span>' : ''}
            </div>
            <div class="text-xs text-gray-500"><i class="far fa-clock"></i> ${event.time}</div>
            <div class="text-xs text-gray-500"><i class="fas fa-user-md"></i> ${event.doctor}</div>
        `;
        dateAppointmentsList.appendChild(evDiv);
    });
}

// --- DELETE ACTION

let formToSubmit = null; 

function confirmDelete(buttonElement) {
    // Find the specific form that contains this button
    formToSubmit = buttonElement.closest('form');
    
    // Show the modal
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.add('active');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.remove('active');
    formToSubmit = null; 
}

// Attach the actual submission to the "Yes, Delete" button
document.addEventListener("DOMContentLoaded", function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    }

    // Close modal if they click outside the box
    const modalOverlay = document.getElementById('deleteConfirmModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    }
});

/* Edit Action */
function openModal(id) {
    const modal = document.getElementById(id);

    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);

    if (modal) {
        modal.classList.remove('active');
    }
}

function editPatient(id, name, email, date, phone, doctor, isConfirmed) {
    if (Number(isConfirmed) === 1) {
        alert("This appointment is already confirmed and cannot be edited.");
        return;
    }

    document.getElementById('edit_patient_id_display').value = id;
    document.getElementById('edit_db_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_doctor').value = doctor;

    openModal('editModal');
}

/* Confirmation */
function confirmAppointment(patientId, doctorAssigned) {
    if (!doctorAssigned || doctorAssigned === "") {
        // show modal instead
        document.getElementById("doctorRequiredModal").classList.remove("hidden");

        // attach redirect
        document.getElementById("goToEditBtn").onclick = function () {
            window.location.href = "edit_patient.php?id=" + patientId;
        };

        return;
    }

    // proceed with actual confirmation
    proceedConfirmation(patientId);
}

function closeDoctorModal() {
    document.getElementById("doctorRequiredModal").classList.add("hidden");
}

function proceedConfirmation(patientId) {
    const form = event.target.closest('form');
    form.submit();
}

/* Done Appointment */