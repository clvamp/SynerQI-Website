// CALENDAR RELATED
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

    document.querySelectorAll('.confirm-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // 🔥 THIS stops the row click issue
            confirmAppointment(this, this.dataset.id);
        });
    });

    // Close modal if they click outside the box
    const modalOverlay = document.getElementById('deleteConfirmModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    }
});

// --- EDIT ACTION
function editPatient(id, name, email, date, phone, doctor, vitals, concern, isConfirmed) {
    // Clean the ID so PHP receives a number (e.g., "SYNERQI-0001" -> "1")
    const numericId = id.toString().replace('SYNERQI-', '').replace(/^0+/, '');
    const formattedId = 'SYNERQI-' + String(numericId).padStart(4, '0');

    if (document.getElementById('edit_db_id')) {
        document.getElementById('edit_db_id').value = numericId;
    }

    if (document.getElementById('edit_patient_id_display')) {
        document.getElementById('edit_patient_id_display').value = formattedId;
    }
    
    // Populate the rest of the fields
    if (document.getElementById('edit_name')) document.getElementById('edit_name').value = name;
    if (document.getElementById('edit_email')) document.getElementById('edit_email').value = email;
    if (document.getElementById('edit_date')) document.getElementById('edit_date').value = date;
    if (document.getElementById('edit_phone')) document.getElementById('edit_phone').value = phone;
    if (document.getElementById('edit_doctor')) document.getElementById('edit_doctor').value = doctor;
    if (document.getElementById('edit_patient_vitals')) document.getElementById('edit_patient_vitals').value = vitals;
    if (document.getElementById('edit_patient_concern')) document.getElementById('edit_patient_concern').value = concern;

    openModal('editModal');
}

function handleConfirmClick(buttonElement) {
    if (!buttonElement) return;

    const form = buttonElement.closest('form');
    const isEditModalConfirm = !!buttonElement.closest('#editModal');

    const validation = validateAppointmentFields({
        mode: isEditModalConfirm ? 'modal' : 'row',
        nameField: isEditModalConfirm ? document.getElementById('edit_name') : null,
        emailField: isEditModalConfirm ? document.getElementById('edit_email') : null,
        phoneField: isEditModalConfirm ? document.getElementById('edit_phone') : null,
        dateField: isEditModalConfirm ? document.getElementById('edit_date') : null,
        doctorField: isEditModalConfirm ? document.getElementById('edit_doctor') : null,
        vitalsField: isEditModalConfirm ? document.getElementById('edit_patient_vitals') : null,
        concernField: isEditModalConfirm ? document.getElementById('edit_patient_concern') : null,
        rowDoctor: buttonElement.dataset.doctor,
        rowVitals: buttonElement.dataset.vitals,
        rowConcern: buttonElement.dataset.concern,
        postNoticeAction: !isEditModalConfirm ? () => openEditModalFromRow(buttonElement) : null
    });

    if (!validation.isValid) {
        return;
    }

    if (form) {
        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput) {
            actionInput.value = 'confirm_patient';
        }
        form.submit();
    }
}

function openEditModalFromRow(buttonElement) {
    const id = buttonElement.dataset.id;
    const name = buttonElement.dataset.name || '';
    const email = buttonElement.dataset.email || '';
    const date = buttonElement.dataset.date || '';
    const phone = buttonElement.dataset.phone || '';
    const doctor = buttonElement.dataset.doctor || '';
    const vitals = buttonElement.dataset.vitals || '';
    const concern = buttonElement.dataset.concern || '';

    editPatient(
        id,
        name,
        email,
        date,
        phone,
        doctor,
        vitals,
        concern,
        0
    );
}

function validateAppointmentFields(fields) {
    const noticeModal = document.getElementById('validationNoticeModal');
    const noticeConfirmBtn = document.getElementById('noticeConfirmBtn');
    const noticeMessage = document.getElementById('noticeMessage');

    let missingField = null;
    let warningText = '';

    const invalidDoctorValues = ['select doctor', 'n/a', 'pending assignment', 'not assigned'];

    function isInvalidDoctorValue(value) {
        if (!value || !value.toString().trim()) return true;
        return invalidDoctorValues.includes(value.toString().trim().toLowerCase());
    }

    if (fields.mode === 'modal') {
        if (fields.nameField && !fields.nameField.value.trim()) {
            missingField = fields.nameField;
            warningText = 'Please enter the Patient Name.';
        } else if (fields.emailField && !fields.emailField.value.trim()) {
            missingField = fields.emailField;
            warningText = 'Please enter the Patient Email.';
        } else if (fields.phoneField && !fields.phoneField.value.trim()) {
            missingField = fields.phoneField;
            warningText = 'Please enter the Patient Telephone Number.';
        } else if (fields.dateField && !fields.dateField.value.trim()) {
            missingField = fields.dateField;
            warningText = 'Please set the Appointment Date.';
        } else if (fields.doctorField && isInvalidDoctorValue(fields.doctorField.value)) {
            missingField = fields.doctorField;
            warningText = 'Please assign a Doctor before confirming.';
        } else if (fields.vitalsField && !fields.vitalsField.value.trim()) {
            missingField = fields.vitalsField;
            warningText = 'Please record the Patient Vitals before confirming.';
        } else if (fields.concernField && !fields.concernField.value.trim()) {
            missingField = fields.concernField;
            warningText = 'Please record the Patient Concern/Symptoms.';
        }
    } else {
        if (isInvalidDoctorValue(fields.rowDoctor)) {
            warningText = 'Cannot confirm appointment: no doctor assigned.';
        } else if (!fields.rowVitals || !fields.rowVitals.trim()) {
            warningText = 'Cannot confirm appointment: patient vitals are missing.';
        } else if (!fields.rowConcern || !fields.rowConcern.trim()) {
            warningText = 'Cannot confirm appointment: patient concern/symptoms are missing.';
        }
    }

    if (!warningText) {
        return { isValid: true };
    }

    if (!noticeModal || !noticeMessage || !noticeConfirmBtn) {
        alert(warningText);
        return { isValid: false };
    }

    noticeMessage.textContent = warningText;
    noticeModal.style.display = 'flex';
    noticeModal.classList.add('active');

    noticeConfirmBtn.onclick = function () {
        noticeModal.classList.remove('active');
        noticeModal.style.display = 'none';

        if (fields.postNoticeAction && typeof fields.postNoticeAction === 'function') {
            fields.postNoticeAction();
        }

        if (missingField) {
            setTimeout(() => {
                missingField.classList.add('validation-highlight', 'ring-2', 'ring-red-400', 'border-red-500');
                missingField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                missingField.focus();

                setTimeout(() => {
                    missingField.classList.remove('validation-highlight', 'ring-2', 'ring-red-400', 'border-red-500');
                }, 2500);
            }, 250);
        }
    };

    return { isValid: false };
}

// --- DONE MODAL ---
function openDoneModal(dbId) {
    const input = document.getElementById('done_db_id');
    const modal = document.getElementById('doneConfirmModal');

    if (input && modal) {
        // Inject the patient ID into the hidden field
        input.value = dbId;
        // Show the modal using your existing CSS class
        modal.classList.add('active');
    }
}

function closeDoneModal() {
    const modal = document.getElementById('doneConfirmModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Global listener to close modal when clicking outside the box
window.addEventListener('click', function(e) {
    const doneModal = document.getElementById('doneConfirmModal');
    if (e.target === doneModal) {
        closeDoneModal();
    }
});

// VIEW MODAL
function viewPatient(displayId, name, email, date, phone, doctor, vitals, concern) {
    // Fill the View Modal fields
    document.getElementById('view_patient_id').value = displayId;
    document.getElementById('view_name').value = name;
    document.getElementById('view_email').value = email;
    document.getElementById('view_date').value = date;
    document.getElementById('view_phone').value = phone;
    document.getElementById('view_doctor').value = doctor;
    document.getElementById('view_patient_vitals').value = vitals;
    document.getElementById('view_patient_concern').value = concern;

    // Show the View Modal
    const modal = document.getElementById('viewModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

// Function to close the modal (if you don't have a generic one)
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function setupViewModalOutsideClick() {
    const viewModal = document.getElementById('viewModal');
    if (!viewModal) return;

    viewModal.addEventListener('click', function (e) {
        if (e.target === viewModal) {
            closeModal('viewModal');
        }
    });
}

document.addEventListener('DOMContentLoaded', setupViewModalOutsideClick);

// =========================================
// CONFIRMATION VALIDATION (EDIT MODAL)
// Validates ALL required fields before saving:
// Name, Email, Date, Doctor, Vitals, Concern
// =========================================
document.addEventListener("DOMContentLoaded", function () {

    // Correctly target your EDIT modal form
    const adminForm = document.querySelector('#editModal form');

    const noticeModal = document.getElementById("validationNoticeModal");
    const noticeConfirmBtn = document.getElementById("noticeConfirmBtn");
    const noticeMessage = document.getElementById("noticeMessage");

    if (!adminForm) return;

    adminForm.setAttribute("novalidate", "");

    adminForm.addEventListener("submit", function (e) {

        let missingField = null;
        let warningText = "";

        // =========================================
        // FIELD REFERENCES
        // =========================================
        const nameField = document.getElementById("edit_name");
        const emailField = document.getElementById("edit_email");
        const phoneField = document.getElementById("edit_phone");
        const dateField = document.getElementById("edit_date");
        const doctorField = document.getElementById("edit_doctor");
        const vitalsField = document.getElementById("edit_patient_vitals");
        const concernField = document.getElementById("edit_patient_concern");

        // =========================================
        // VALIDATION ORDER
        // =========================================
        if (nameField && !nameField.value.trim()) {
            missingField = nameField;
            warningText = "Please enter the Patient Name.";
        }

        else if (emailField && !emailField.value.trim()) {
            missingField = emailField;
            warningText = "Please enter the Patient Email.";
        }

        else if (phoneField && !phoneField.value.trim()) {
            missingField = phoneField;
            warningText = "Please enter the Patient Telephone Number.";
        }

        else if (dateField && !dateField.value.trim()) {
            missingField = dateField;
            warningText = "Please set the Appointment Date.";
        }

        else if (
            doctorField &&
            (
                !doctorField.value.trim() ||
                doctorField.value === "" ||
                doctorField.value === "Select Doctor" ||
                doctorField.value === "N/A"
            )
        ) {
            missingField = doctorField;
            warningText = "Please assign a Doctor before saving.";
        }

        else if (vitalsField && !vitalsField.value.trim()) {
            missingField = vitalsField;
            warningText = "Please record the Patient Vitals before saving.";
        }

        else if (concernField && !concernField.value.trim()) {
            missingField = concernField;
            warningText = "Please record the Patient Concern/Symptoms.";
        }

        // =========================================
        // SHOW CUSTOM WARNING MODAL
        // =========================================
        if (missingField) {
            e.preventDefault();

            noticeMessage.textContent = warningText;

            // Make modal visible
            noticeModal.style.display = "flex";
            noticeModal.classList.add("active");

            noticeConfirmBtn.onclick = function () {

                noticeModal.classList.remove("active");
                noticeModal.style.display = "none";

                setTimeout(() => {

                    // Highlight field
                    missingField.classList.add(
                        "validation-highlight",
                        "ring-2",
                        "ring-red-400",
                        "border-red-500"
                    );

                    // Scroll into view
                    missingField.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });

                    // Focus
                    missingField.focus();

                    // Remove highlight after delay
                    setTimeout(() => {
                        missingField.classList.remove(
                            "validation-highlight",
                            "ring-2",
                            "ring-red-400",
                            "border-red-500"
                        );
                    }, 2500);

                }, 250);
            };
        }
    });
});