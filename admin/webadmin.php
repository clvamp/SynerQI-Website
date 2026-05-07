<?php
session_start();

// DATABASE CONNECTION - Using empty string for XAMPP root password
$conn = new mysqli('127.0.0.1', 'root', '', 'synerqi_db');

if ($conn->connect_error) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>
            <h2 style='color:red;'>Database Connection Failed!</h2>
            <p>Error details: " . $conn->connect_error . "</p>
         </div>");
}

// --- HARDCODED ADMIN LOGIN ---
$admin_user = 'admin';
$admin_pass = 'synerqi2026';
$doctors = ['Dr. Elizabeth', 'Dr. Kim', 'Dr. Dacanay', 'Dr. Tee Wo Chin'];

// FORM HANDLING (CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Login Action
        if ($action === 'login') {
            if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: webadmin.php");
                exit;
            } else {
                $login_error = "Invalid username or password.";
            }
        }
        
        // Logout Action
        if ($action === 'logout') {
            session_destroy();
            header("Location: webadmin.php");
            exit;
        }

        // Add Patient
        if ($action === 'add_patient') {
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['userEmail']); 
            $date = $conn->real_escape_string($_POST['date']);
            $phone = $conn->real_escape_string($_POST['phone']);
            $doctor = $conn->real_escape_string($_POST['doctor']);
            
            // NEW FIELDS
            $vitals = $conn->real_escape_string($_POST['vitals']);
            $concern = $conn->real_escape_string($_POST['concern']);

            // Combine selected services into a single string
            $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : "";
            $totalAmount = isset($_POST['totalAmount']) ? intval($_POST['totalAmount']) : 0;

            // Updated INSERT query with vitals and concern
            $conn->query("INSERT INTO online_appointments 
                (patient_name, userEmail, date, telephone, assigned_doctor, vitals, concern, services, totalAmount, status) 
                VALUES 
                ('$name', '$email', '$date', '$phone', '$doctor', '$vitals', '$concern', '$services', '$totalAmount', 'Pending')");

            header("Location: webadmin.php?page=online");
            exit;
        }

        // Edit Patient
        if ($action === 'edit_patient' && isset($_SESSION['admin_logged_in'])) {
            $db_id = (int)$_POST['db_id']; 
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['userEmail']); 
            $date = $conn->real_escape_string($_POST['date']);
            $phone = $conn->real_escape_string($_POST['phone']);
            $doctor = $conn->real_escape_string($_POST['doctor']);
            
            // NEW FIELDS
            $vitals = $conn->real_escape_string($_POST['patient_vitals']);
            $concern = $conn->real_escape_string($_POST['patient_concern']);
            
            $status = $conn->real_escape_string($_POST['status'] ?? 'Pending');

            // Updated UPDATE query with vitals and concern
            $conn->query("UPDATE online_appointments 
                        SET patient_name='$name',
                            userEmail='$email',
                            date='$date',
                            telephone='$phone',
                            assigned_doctor='$doctor',
                            vitals='$vitals',
                            concern='$concern',
                            status='$status'
                        WHERE id=$db_id");

            header("Location: webadmin.php?page=online");
            exit;
        }

        // Confirm Patient
        if ($action === 'confirm_patient') {
            $db_id = intval($_POST['db_id']);
            $conn->query("UPDATE online_appointments SET status = 'Confirmed' WHERE id = $db_id");

            header("Location: webadmin.php?page=online");
            exit;
        }

        // Done Appointment
        if ($action === 'done_appointment') {
            $db_id = intval($_POST['db_id']);
            
            // 1. Copy to archive (Assumes archive_appointments has the same structure)
            $copyQuery = "INSERT INTO archive_appointments SELECT * FROM online_appointments WHERE id = $db_id";
            
            if ($conn->query($copyQuery)) {
                // 2. Delete from current table
                $conn->query("DELETE FROM online_appointments WHERE id = $db_id");
            }
            
            header("Location: webadmin.php?page=online");
            exit;
        }

        // Delete Patient
        if ($action === 'delete_patient') {
            $db_id = intval($_POST['db_id']);

            $conn->query("DELETE FROM online_appointments WHERE id = $db_id");

            $result = $conn->query("SELECT COUNT(*) as total FROM online_appointments");
            $row = $result->fetch_assoc();

            if ($row['total'] == 0) {
                $conn->query("ALTER TABLE online_appointments AUTO_INCREMENT = 1");
            }

            header("Location: webadmin.php?page=online");
            exit;
        }
    }
}

// --- ROUTING & DATA FETCHING ---
$current_page = $_GET['page'] ?? 'dashboard';
$patients = [];

if (isset($_SESSION['admin_logged_in'])) {
    // Save chosen sorting mode to session so it persists across updates/deletions/redirects
    if (isset($_GET['sort'])) {
        $_SESSION['sort_mode'] = $_GET['sort'];
    }
    
    // Default to 'date' sorting mode if not set
    $sort = $_SESSION['sort_mode'] ?? 'date';

    if ($sort === 'priority') {
        // Priority Sort: Confirmed stays first, then Pending, then Cancelled/others, sorted by latest date within groups
        $query = "SELECT * FROM online_appointments ORDER BY CASE WHEN status = 'Confirmed' THEN 0 WHEN status = 'Pending' THEN 1 ELSE 2 END, date DESC";
    } else {
        // Date Sort: STRICT CHRONOLOGICAL SORT - Sorted strictly by appointment date and time (Latest on top)
        $query = "SELECT * FROM online_appointments ORDER BY date DESC";
    }

    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SynerQi Admin Panel</title>
    <link rel="stylesheet" href="design/webadmin.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#008080', primaryDark: '#006666' },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .modal { display: none; }
        .modal.active { display: flex; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <div class="w-full h-full flex items-center justify-center bg-gray-100">
            <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-200">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-primary mb-2">SynerQi Admin</h2>
                    <p class="text-sm text-gray-500">Sign in to manage appointments</p>
                </div>
                
                <?php if (isset($login_error)): ?>
                    <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm text-center">
                        <?php echo $login_error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-primaryDark text-white font-medium py-2.5 rounded-lg transition duration-200 mt-4 shadow-md">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-full shadow-sm z-10">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-primary tracking-tight">SynerQi.</h1>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-semibold">Admin Portal</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-1">
                <a href="?page=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo $current_page === 'dashboard' ? 'bg-teal-50 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-primary'; ?>">
                    <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="?page=online" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo $current_page === 'online' ? 'bg-teal-50 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-primary'; ?>">
                    <i class="fas fa-calendar-check w-5 text-center"></i> Online Appointments
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors opacity-90 cursor-not-allowed text-gray-400" title="Coming Soon">
                    <i class="fas fa-calendar-check w-5 text-center"></i> Storage
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <form method="POST">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg transition font-medium text-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main id="main-content" class="flex-1 h-full overflow-y-auto bg-gray-50/50 p-8">
            <div class="max-w-6xl mx-auto">
                
                <?php if ($current_page === 'dashboard'): ?>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-primary text-xl">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Patients</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo count($patients); ?></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <?php
                                    $today_count = 0;
                                    $today = date('Y-m-d');
                                    foreach($patients as $p) {
                                        if (strpos($p['date'], $today) === 0) $today_count++;
                                    }
                                ?>
                                <p class="text-sm text-gray-500 font-medium">Appointments Today</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo $today_count; ?></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Active Doctors</p>
                                <p class="text-2xl font-bold text-gray-800"><?php echo count($doctors); ?></p>
                            </div>
                        </div>
                    </div>

                    <!--DASHBOARD ANALYTICS -->








                    <!-- END HERE -->

                <?php elseif ($current_page === 'online'): ?>
                    
                    <?php
                        $calendar_data = [];
                        foreach ($patients as $p) {
                            $date_val = $p['date'] ?? null;
                            
                            // Check for both 'userName' and 'patient_name' depending on how it was inserted
                            $name_val = !empty($p['patient_name']) ? $p['patient_name'] : (!empty($p['userName']) ? $p['userName'] : 'Unknown');
                            $doctor_val = !empty($p['assigned_doctor']) ? $p['assigned_doctor'] : 'N/A';

                            if ($date_val) {
                                $parsedDate = date('Y-m-d', strtotime($date_val));
                                $parsedTime = !empty($p['bookingTime']) ? $p['bookingTime'] : date('h:i A', strtotime($date_val));
                                
                                if (!isset($calendar_data[$parsedDate])) {
                                    $calendar_data[$parsedDate] = [];
                                }
                                $calendar_data[$parsedDate][] = [
                                    'name' => $name_val,
                                    'time' => $parsedTime,
                                    'doctor' => $doctor_val
                                ];
                            }
                        }
                    ?>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="far fa-calendar-alt text-primary"></i> Appointment Calendar
                                </h3>
                                <div class="flex items-center gap-3">
                                    <button onclick="prevMonth()" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition"><i class="fas fa-chevron-left"></i></button>
                                    <span id="monthYearDisplay" class="font-medium text-gray-700 w-32 text-center"></span>
                                    <button onclick="nextMonth()" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-2 text-center font-medium text-gray-400 text-sm mb-2">
                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="calendarDays" class="grid grid-cols-7 gap-2 text-center text-sm">
                                </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Scheduled Patients</h3>
                            <p id="selectedDateDisplay" class="text-sm text-gray-400 border-b border-gray-100 pb-3 mb-4">Select a date to view appointments.</p>
                            
                            <div id="dateAppointmentsList" class="space-y-3 overflow-y-auto flex-1 max-h-[300px] pr-2">
                                <p class="text-sm text-gray-400 italic mt-4 text-center">No date selected.</p>
                            </div>
                        </div>
                    </div>

                    <div class="masterlist-wrapper">
                                    <div class="masterlist-header">
                                        
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <h3 class="masterlist-title" style="margin: 0;">Patient Masterlist</h3>
                                            
                                            <div style="display: flex; align-items: center; background-color: #f3f4f6; padding: 4px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                                <?php $sort_mode = $_SESSION['sort_mode'] ?? 'date'; ?>
                                                
                                                <a href="?page=online&sort=date" 
                                                style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; transition: all 0.2s; <?php echo $sort_mode === 'date' ? 'background-color: #008080; color: white;' : 'color: #6b7280;'; ?>"
                                                onmouseover="if(this.style.backgroundColor !== 'rgb(0, 128, 128)') this.style.backgroundColor='#e5e7eb';"
                                                onmouseout="if(this.style.backgroundColor !== 'rgb(0, 128, 128)') this.style.backgroundColor='transparent';"
                                                title="Sort by Date (Priority on Top)">
                                                    <i class="fas fa-calendar-alt" style="font-size: 0.875rem;"></i>
                                                </a>
                                                
                                                <a href="?page=online&sort=priority" 
                                                style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; transition: all 0.2s; <?php echo $sort_mode === 'priority' ? 'background-color: #008080; color: white;' : 'color: #6b7280;'; ?>"
                                                onmouseover="if(this.style.backgroundColor !== 'rgb(0, 128, 128)') this.style.backgroundColor='#e5e7eb';"
                                                onmouseout="if(this.style.backgroundColor !== 'rgb(0, 128, 128)') this.style.backgroundColor='transparent';"
                                                title="Sort strictly by Priority Status">
                                                    <i class="fas fa-crown" style="font-size: 0.875rem;"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="masterlist-tools">
                                            <button onclick="openAddPatientModal()" class="addPatientBtn">
                                                + Encode Patient
                                            </button>

                                            <div class="search-box">
                                                <i class="fas fa-search search-icon"></i>
                                                <input type="text" id="searchInput" onkeyup="filterTable()"
                                                    placeholder="Search patients..."
                                                    class="search-input">
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="table-wrap">
                                <table class="masterlist-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Service</th>
                                            <th>Doctor</th>
                                            <th>Schedule</th>
                                            <th>Amount</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="patientTableBody">
                                        <?php foreach ($patients as $p): ?>
                                        <tr class="patient-row" 
                                            style="<?php echo ($p['status'] === 'Confirmed') ? 'background-color: #fff9db !important; border-left: 4px solid #f59e0b; cursor: not-allowed;' : ''; ?>"
                                            
                                            <?php if (($p['status'] ?? 'Pending') !== 'Confirmed'): ?>
                                                onclick="editPatient(
                                                    'SYNERQI-<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?>',
                                                    '<?php echo htmlspecialchars($p['patient_name'] ?? 'Unknown', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($p['userEmail'] ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo isset($p['date']) ? date('Y-m-d\TH:i', strtotime($p['date'])) : ''; ?>',
                                                    '<?php echo htmlspecialchars($p['telephone'] ?? 'N/A', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($p['assigned_doctor'] ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($p['status'] ?? 'Pending', ENT_QUOTES); ?>'
                                                )"
                                            <?php endif; ?>
                                        >

                                            <td class="id-cell">
                                                SYNERQI-<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?>
                                            </td>

                                            <td class="name-cell">
                                                <?php echo htmlspecialchars(!empty($p['patient_name']) ? $p['patient_name'] : (!empty($p['userName']) ? $p['userName'] : 'Unknown')); ?>
                                            </td>

                                            <td>
                                                <div>
                                                    <?php echo htmlspecialchars($p['userEmail'] ?? 'N/A'); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    <?php echo htmlspecialchars(!empty($p['telephone']) ? $p['telephone'] : (!empty($p['userPhone']) ? $p['userPhone'] : 'N/A')); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="service-badge">
                                                    <?php echo htmlspecialchars(!empty($p['services']) ? $p['services'] : (!empty($p['serviceSelect']) ? $p['serviceSelect'] : 'Consultation')); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="doctor-cell">
                                                    <?php echo htmlspecialchars($p['assigned_doctor'] ?? 'Not Assigned'); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="date-main">
                                                    <?php echo isset($p['date']) ? date('M d, Y', strtotime($p['date'])) : ''; ?>
                                                </div>

                                                <div class="time-sub">
                                                    <?php echo isset($p['date']) ? date('h:i A', strtotime($p['date'])) : ''; ?>
                                                </div>
                                            </td>

                                            <td class="amount-cell">
                                                ₱<?php echo number_format($p['totalAmount'] ?? 0); ?>
                                            </td>

                                            <td>
                                                <div class="action-wrap" style="display: flex; align-items: center; gap: 12px; justify-content: center;">
                                                    
                                                    <?php if (($p['status'] ?? 'Pending') === 'Confirmed'): ?>
                                                        <form method="POST" style="margin: 0;" onsubmit="return confirm('Archive this appointment?');">
                                                            <input type="hidden" name="action" value="done_appointment">
                                                            <input type="hidden" name="db_id" value="<?php echo $p['id']; ?>">
                                                            <button type="submit" 
                                                                    onclick="event.stopPropagation();" 
                                                                    style="color: #D4AF37; background: none; border: none; cursor: pointer; padding: 4px;" 
                                                                    title="Appointment Done">
                                                                <i class="fas fa-check-double" style="font-size: 1.15rem;"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" style="margin: 0;">
                                                            <input type="hidden" name="action" value="confirm_patient">
                                                            <input type="hidden" name="db_id" value="<?php echo $p['id']; ?>">
                                                            <button type="submit" 
                                                                    onclick="event.stopPropagation();" 
                                                                    style="color: #059669; background: none; border: none; cursor: pointer;" 
                                                                    title="Confirm Appointment">
                                                                <i class="fas fa-check-circle" style="font-size: 1.15rem;"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <form method="POST" style="margin: 0;">
                                                        <input type="hidden" name="action" value="delete_patient">
                                                        <input type="hidden" name="db_id" value="<?php echo $p['id']; ?>">

                                                        <button type="button"
                                                                onclick="event.stopPropagation(); confirmDelete(this)"
                                                                class="delete-btn">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                <?php endif; ?>
            </div>
        </main>
        

        <!--ADD MODAL-->
        <div id="addModal" class="modal fixed inset-0 z-50 overflow-visible bg-black/50 items-center justify-center">
            
            <div class="bg-white rounded-2xl p-6 w-full max-w-5xl shadow-2xl relative">

                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        New Patient
                    </h2>

                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    
                    <input type="hidden" name="action" value="add_patient">
                    <input type="hidden" name="db_id" id="add_db_id">

                    <div class="flex flex-col md:flex-row overflow-hidden rounded-2xl border border-gray-200">

                        <!-- LEFT MAIN CARD -->
                        <div class="w-full md:w-3/5 bg-white p-6">

                            <div class="space-y-4">

                                <!-- Patient ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient ID
                                    </label>

                                    <input 
                                        type="text" 
                                        id="add_patient_id_display" 
                                        readonly 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 text-sm cursor-not-allowed"
                                    >
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient Name
                                    </label>

                                    <input 
                                        type="text" 
                                        name="name" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>

                                    <input 
                                        type="email" 
                                        name="userEmail" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <!-- Treatments -->
                                <div class="form-group relative">

                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Treatments
                                    </label>

                                    <div class="custom-select-wrapper border border-gray-300 rounded-lg cursor-pointer">

                                        <div 
                                            class="select-box-trigger px-3 py-2 flex justify-between items-center bg-white"
                                            id="serviceToggleButton"
                                        >

                                            <span id="selectedServicesText" class="text-sm text-gray-500">
                                                Select Treatments
                                            </span>

                                            <span class="material-icons-round text-gray-400">
                                                expand_more
                                            </span>
                                        </div>

                                        <div 
                                            id="servicesSelectionOverlay"
                                            class="services-dropdown-content hidden border-t border-gray-200 bg-white max-h-56 overflow-y-auto p-2"
                                        >

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Consultation" data-price="1000">
                                                <span>Consultation - ₱1,000</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Acupuncture" data-price="1500">
                                                <span>Acupuncture - ₱1,500</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Herbal Remedies" data-price="300">
                                                <span>Herbal Remedies - ₱300</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Cupping" data-price="1200">
                                                <span>Cupping - ₱1,200</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Tuina" data-price="1000">
                                                <span>Tuina - ₱1,000</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Guasha" data-price="1200">
                                                <span>Guasha - ₱1,200</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Rehabilitation" data-price="1000">
                                                <span>Rehabilitation - ₱1,000</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="Physical Exercise" data-price="800">
                                                <span>Physical Exercise - ₱800</span>
                                            </label>

                                            <label class="check-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer text-sm">
                                                <input type="checkbox" name="services[]" class="service-check" value="3D Acu & Myofascial" data-price="4000">
                                                <span>3D Acu & Myofascial - ₱4,000</span>
                                            </label>

                                        </div>
                                    </div>

                                    <p id="servicesError" class="services-error hidden">
                                        Please select at least 1 treatment.
                                    </p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Telephone
                                    </label>

                                    <input 
                                        type="text" 
                                        name="phone" 
                                        placeholder="09XXXXXXXXX" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <!-- Date + Doctor -->
                                <div class="grid grid-cols-2 gap-4">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Appointment Date
                                        </label>

                                        <input 
                                            type="datetime-local" 
                                            name="date" 
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Doctor
                                        </label>

                                        <select 
                                            name="doctor" 
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white"
                                        >

                                            <option value="">Select Doctor</option>

                                            <?php foreach($doctors as $doc): ?>
                                                <option value="<?php echo htmlspecialchars($doc); ?>">
                                                    <?php echo htmlspecialchars($doc); ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                </div>

                                <!-- SUBMIT -->
                                <button 
                                    type="submit"
                                    name="submit_add"
                                    class="w-full bg-teal-600 text-white py-3 rounded-xl font-semibold hover:bg-teal-700 transition mt-6"
                                >
                                    Confirm Enrollment
                                </button>

                            </div>
                        </div>

                        <!-- RIGHT NOTES PANEL -->
                        <div class="w-full md:w-2/5 bg-gray-50 border-l border-gray-200 p-6">

                            <div class="mb-5">

                                <h3 class="text-lg font-semibold text-gray-800">
                                    Patient Notes
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Medical observations and consultation details.
                                </p>

                            </div>

                            <div class="space-y-5">

                                <!-- VITALS -->
                                <div>

                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient Vitals
                                    </label>

                                    <textarea 
                                        name="patient_vitals"
                                        rows="6"
                                        required
                                        placeholder="Blood Pressure, Heart Rate, Temperature..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-teal-500 outline-none text-sm resize-none"
                                    ></textarea>

                                </div>

                                <!-- CONCERN -->
                                <div>

                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient Concern
                                    </label>

                                    <textarea 
                                        name="patient_concern"
                                        rows="6"
                                        required
                                        placeholder="Describe symptoms, pain, concerns..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-teal-500 outline-none text-sm resize-none"
                                    ></textarea>

                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!--EDIT MODAL-->
        <div id="editModal" class="modal fixed inset-0 z-50 overflow-visible bg-black/50 items-center justify-center">
            <div class="bg-white rounded-2xl p-6 w-full max-w-5xl shadow-2xl relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Edit Patient</h2>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>

                <form action="webadmin.php" method="POST">
                    <input type="hidden" name="action" value="edit_patient">
                    <input type="hidden" name="db_id" id="edit_db_id">

                    <div class="flex flex-col md:flex-row overflow-hidden rounded-2xl border border-gray-200">

                        <!-- LEFT MAIN CARD -->
                        <div class="w-full md:w-3/5 bg-white p-6">

                            <div class="space-y-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>

                                    <input 
                                        type="text"
                                        id="edit_patient_id_display"
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 outline-none text-sm cursor-not-allowed"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name</label>

                                    <input 
                                        type="text"
                                        name="name"
                                        id="edit_name"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="userEmail" id="edit_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>

                                    <input 
                                        type="text"
                                        name="phone"
                                        id="edit_phone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>

                                    <input 
                                        type="datetime-local"
                                        name="date"
                                        id="edit_date"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>

                                    <select 
                                        name="doctor"
                                        id="edit_doctor"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                    >
                                        <?php foreach($doctors as $doc) echo "<option>$doc</option>"; ?>
                                    </select>
                                </div>

                                <!-- SAVE BUTTON -->
                                <button 
                                    type="submit"
                                    class="w-full bg-teal-600 text-white py-3 rounded-xl font-semibold hover:bg-teal-700 transition mt-6"
                                >
                                    Save Changes
                                </button>

                            </div>
                        </div>

                        <!-- RIGHT NOTES PANEL -->
                        <div class="w-full md:w-2/5 bg-gray-50 border-l border-gray-200 p-6">

                            <div class="mb-5">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Patient Notes
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Medical observations and consultation details.
                                </p>
                            </div>

                            <div class="space-y-5">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient Vitals
                                    </label>

                                    <textarea 
                                        name="patient_vitals"
                                        id="edit_patient_vitals"
                                        rows="6"
                                        required
                                        placeholder="Blood Pressure, Heart Rate, Temperature..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-teal-500 outline-none text-sm resize-none"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Patient Concern
                                    </label>

                                    <textarea 
                                        name="patient_concern"
                                        id="edit_patient_concern"
                                        rows="6"
                                        required
                                        placeholder="Describe symptoms, pain, concerns..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-teal-500 outline-none text-sm resize-none"
                                    ></textarea>
                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <script src="script/adminscript.js" type="text/javascript"></script>
        <script>
<?php if ($current_page === 'online'): ?>
    let nav = 0;
    const calendarEvents = <?php echo json_encode($calendar_data); ?>;
    const calendarDays = document.getElementById('calendarDays');
    const monthYearDisplay = document.getElementById('monthYearDisplay');
    const selectedDateDisplay = document.getElementById('selectedDateDisplay');
    const dateAppointmentsList = document.getElementById('dateAppointmentsList');

    function nextMonth() { nav++; loadCalendar(); }
    function prevMonth() { nav--; loadCalendar(); }

    document.addEventListener('DOMContentLoaded', loadCalendar);
    <?php endif; ?>
</script>
    <?php endif; ?>

    <div id="doctorRequiredModal" class="delete-overlay hidden">
        <div class="delete-modal-box">
            <div class="mb-4 text-yellow-500">
                <i class="fas fa-user-md text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">
                Doctor not assigned
            </h3>
            <p class="text-gray-500 mb-6 text-sm">
                Please assign a doctor first before confirming this appointment.
            </p>
            
            <div class="flex justify-center gap-3">
                <button onclick="closeDoctorModal()" class="btn-cancel">
                    Cancel
                </button>
                <button id="goToEditBtn" class="btn-confirm">
                    Go to Edit
                </button>
            </div>
        </div>
    </div>

    <div id="deleteConfirmModal" class="delete-overlay">
        <div class="delete-modal-box">
            <div class="mb-4 text-red-500">
                <i class="fas fa-exclamation-triangle text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete this data?</h3>
            <p class="text-gray-500 mb-6 text-sm">This action cannot be undone.</p>
            
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="btn-cancel">No, Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn-confirm">Yes, Delete</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                // 1. Check if a previous scroll position is saved, and jump back to it immediately
                const savedScrollPosition = localStorage.getItem('adminScrollPosition');
                if (savedScrollPosition) {
                    // Small delay ensures Tailwind classes and internal table rows are completely painted first
                    setTimeout(function() {
                        mainContent.scrollTop = parseInt(savedScrollPosition, 10);
                    }, 50);
                }

                // 2. Continuously save scroll position when scrolling
                mainContent.addEventListener('scroll', function() {
                    localStorage.setItem('adminScrollPosition', mainContent.scrollTop);
                });

                // 3. Fallback: Save right before page unloads or redirects
                window.addEventListener('beforeunload', function() {
                    localStorage.setItem('adminScrollPosition', mainContent.scrollTop);
                });
            }
        });
    </script>
</body>
</html>
