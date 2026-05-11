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
            $vitals = $conn->real_escape_string($_POST['patient_vitals']);
            $concern = $conn->real_escape_string($_POST['patient_concern']);

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
            // Ensure we have a valid numeric ID
            $db_id = isset($_POST['db_id']) ? (int)$_POST['db_id'] : 0; 

            if ($db_id > 0) {
                $name = $conn->real_escape_string($_POST['name']);
                $email = $conn->real_escape_string($_POST['userEmail']); 
                $date = $conn->real_escape_string($_POST['date']);
                $phone = $conn->real_escape_string($_POST['phone']);
                $doctor = $conn->real_escape_string($_POST['doctor']);
                $vitals = $conn->real_escape_string($_POST['patient_vitals']);
                $concern = $conn->real_escape_string($_POST['patient_concern']);
                
                // Logic: If a doctor is assigned and vitals are present, 
                // you might want to automatically set status to 'Confirmed' 
                // or keep it as 'Pending' if that's your workflow.
                $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';

                $query = "UPDATE online_appointments 
                        SET patient_name='$name',
                            userEmail='$email',
                            date='$date',
                            telephone='$phone',
                            assigned_doctor='$doctor',
                            vitals='$vitals',
                            concern='$concern',
                            status='$status'
                        WHERE id=$db_id";

                if ($conn->query($query)) {
                    header("Location: webadmin.php?page=online&msg=success");
                } else {
                    // Log error if the query fails
                    error_log("Update failed: " . $conn->error);
                    header("Location: webadmin.php?page=online&msg=error");
                }
                exit;
            }
        }

        // Confirm Patient
        if ($action === 'confirm_patient' && isset($_SESSION['admin_logged_in'])) {
            $db_id = intval($_POST['db_id']);
            $postedDoctor = $conn->real_escape_string($_POST['doctor'] ?? '');
            $postedVitals = $conn->real_escape_string($_POST['patient_vitals'] ?? '');
            $postedConcern = $conn->real_escape_string($_POST['patient_concern'] ?? '');

            // Final server-side safety check using submitted values when present.
            $check = $conn->query("SELECT assigned_doctor, vitals, concern FROM online_appointments WHERE id = $db_id");
            $row = $check->fetch_assoc();

            $effectiveDoctor = trim($postedDoctor) !== '' ? $postedDoctor : ($row['assigned_doctor'] ?? '');
            $effectiveVitals = trim($postedVitals) !== '' ? $postedVitals : ($row['vitals'] ?? '');
            $effectiveConcern = trim($postedConcern) !== '' ? $postedConcern : ($row['concern'] ?? '');

            if (empty($effectiveDoctor) || empty($effectiveVitals) || empty($effectiveConcern)) {
                header("Location: webadmin.php?page=online&msg=missing_info");
                exit;
            }

            $updateFields = "status='Confirmed'";
            if (trim($postedDoctor) !== '') {
                $updateFields .= ", assigned_doctor='$postedDoctor'";
            }
            if (trim($postedVitals) !== '') {
                $updateFields .= ", vitals='$postedVitals'";
            }
            if (trim($postedConcern) !== '') {
                $updateFields .= ", concern='$postedConcern'";
            }

            $conn->query("UPDATE online_appointments SET $updateFields WHERE id=$db_id");
            header("Location: webadmin.php?page=online&msg=confirmed");
            exit;
        }

        // Done Appointment - Move from Online to Archive
        if ($action === 'done_appointment') {
            $db_id = intval($_POST['db_id']);
            
            // 1. Copy the data to the archive table, putting the current 'id' into 'arc_id'
            $moveQuery = "INSERT INTO archive_appointments (arc_id, patient_name, userEmail, date, telephone, assigned_doctor, vitals, concern, services, totalAmount, status) 
                        SELECT id, patient_name, userEmail, date, telephone, assigned_doctor, vitals, concern, services, totalAmount, 'Done' 
                        FROM online_appointments 
                        WHERE id = $db_id";
            
            if ($conn->query($moveQuery)) {
                // 2. After successful copy, delete from the active online table
                $conn->query("DELETE FROM online_appointments WHERE id = $db_id");
                
                header("Location: webadmin.php?page=online");
                exit;
            }
        }

        // Delete Patient
        if ($action === 'delete_patient') {

            $db_id = intval($_POST['db_id']);
            $page = $_GET['page'] ?? 'online';

            // DELETE FROM ONLINE
            if ($page === 'online') {

                $conn->query("DELETE FROM online_appointments WHERE id = $db_id");

                $result = $conn->query("SELECT COUNT(*) as total FROM online_appointments");
                $row = $result->fetch_assoc();

                if ($row['total'] == 0) {
                    $conn->query("ALTER TABLE online_appointments AUTO_INCREMENT = 1");
                }

                header("Location: webadmin.php?page=online");
                exit;
            }

            // DELETE FROM ARCHIVE
            if ($page === 'history') {

                $conn->query("DELETE FROM archive_appointments WHERE id = $db_id");

                $result = $conn->query("SELECT COUNT(*) as total FROM archive_appointments");
                $row = $result->fetch_assoc();

                if ($row['total'] == 0) {
                    $conn->query("ALTER TABLE archive_appointments AUTO_INCREMENT = 1");
                }

                header("Location: webadmin.php?page=history");
                exit;
            }
        }
    }
}

// --- ROUTING & DATA FETCHING ---
$current_page = $_GET['page'] ?? 'dashboard';
$patients = [];

if (isset($_SESSION['admin_logged_in'])) {
    // SAVE SORT MODE
    if (isset($_GET['sort'])) {
        $_SESSION['sort_mode'] = $_GET['sort'];
    }
    $sort = $_SESSION['sort_mode'] ?? 'date';

    // DASHBOARD: pull online appointments so overview cards populate correctly
    if ($current_page === 'dashboard') {
        $query = "
            SELECT *
            FROM online_appointments
            ORDER BY date DESC
        ";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        }
    }

    // ONLINE APPOINTMENTS
    if ($current_page === 'online') {
        if ($sort === 'priority') {
            $query = "
                SELECT *
                FROM online_appointments
                ORDER BY
                    CASE
                        WHEN status = 'Confirmed' THEN 0
                        WHEN status = 'Pending' THEN 1
                        ELSE 2
                    END,
                    date DESC
            ";
        } else {
            $query = "
                SELECT *
                FROM online_appointments
                ORDER BY date DESC
            ";
        }
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        }
    }

    // HISTORY APPOINTMENTS
    if ($current_page === 'history') {
        $query = "
            SELECT *
            FROM archive_appointments
            ORDER BY date DESC
        ";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
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
                <a href="?page=history" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo $current_page === 'history' ? 'bg-teal-50 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-primary'; ?>">
                    <i class="fas fa-calendar-check w-5 text-center"></i> History Appointments
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors opacity-90 cursor-not-allowed text-gray-400" title="Coming Soon">
                    <i class="fas fa-calendar-check w-5 text-center"></i> Inventory
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
                    <?php
                    if (isset($current_page) && $current_page === 'dashboard') {
                        
                        date_default_timezone_set('Asia/Manila');
                        
                        // 1. DATE FILTER & CALENDAR LOGIC
                        $filter_type = isset($_GET['filter']) ? $_GET['filter'] : 'day';
                        $selected_date = isset($_GET['date_select']) ? $_GET['date_select'] : date('Y-m-d');
                        
                        $where_clause = "";
                        if ($filter_type === 'day') {
                            $where_clause = "WHERE DATE(date) = '$selected_date'";
                            $display_label = ($selected_date == date('Y-m-d')) ? "Today" : date('M d, Y', strtotime($selected_date));
                        } elseif ($filter_type === 'month') {
                            $month = date('m', strtotime($selected_date)); 
                            $year = date('Y', strtotime($selected_date));
                            $where_clause = "WHERE MONTH(date) = '$month' AND YEAR(date) = '$year'";
                            $display_label = date('F Y', strtotime($selected_date));
                        } elseif ($filter_type === 'year') {
                            $year = date('Y', strtotime($selected_date));
                            $where_clause = "WHERE YEAR(date) = '$year'";
                            $display_label = $year;
                        }

                        // 2. DATA QUERIES
                        $total_res = $conn->query("SELECT COUNT(*) as total FROM archive_appointments $where_clause");
                        $total_pts = ($total_res) ? $total_res->fetch_assoc()['total'] : 0;

                        // Service Popularity Logic
                        $all_services_res = $conn->query("SELECT services FROM archive_appointments $where_clause");
                        $service_counts = [];
                        $grand_total_services = 0;
                        if ($all_services_res) {
                            while ($row = $all_services_res->fetch_assoc()) {
                                $individual_services = explode(',', $row['services']);
                                foreach ($individual_services as $s) {
                                    $trimmed_s = trim($s);
                                    if (!empty($trimmed_s)) {
                                        $service_counts[$trimmed_s] = ($service_counts[$trimmed_s] ?? 0) + 1;
                                        $grand_total_services++;
                                    }
                                }
                            }
                        }
                        arsort($service_counts);
                        $top_3_services = array_slice($service_counts, 0, 3, true);

                        // Fetch Recent Activity - Limited to 20
                        $recent_pts = $conn->query("SELECT id, patient_name, services, date FROM archive_appointments ORDER BY id DESC LIMIT 20");

                        // Chart Data Logic
                        $chart_labels = [];
                        $chart_data = [];
                        if ($filter_type === 'day') {
                            // Hourly for the day
                            $chart_res = $conn->query("SELECT HOUR(date) as hour, COUNT(*) as count FROM archive_appointments WHERE DATE(date) = '$selected_date' GROUP BY HOUR(date) ORDER BY hour");
                            while ($row = $chart_res->fetch_assoc()) {
                                $chart_labels[] = $row['hour'] . ':00';
                                $chart_data[] = $row['count'];
                            }
                        } elseif ($filter_type === 'month') {
                            $month = date('m', strtotime($selected_date));
                            $year = date('Y', strtotime($selected_date));
                            $chart_res = $conn->query("SELECT DAY(date) as day, COUNT(*) as count FROM archive_appointments WHERE MONTH(date) = '$month' AND YEAR(date) = '$year' GROUP BY DAY(date) ORDER BY day");
                            while ($row = $chart_res->fetch_assoc()) {
                                $chart_labels[] = $row['day'];
                                $chart_data[] = $row['count'];
                            }
                        } elseif ($filter_type === 'year') {
                            $year = date('Y', strtotime($selected_date));
                            $chart_res = $conn->query("SELECT MONTH(date) as month, COUNT(*) as count FROM archive_appointments WHERE YEAR(date) = '$year' GROUP BY MONTH(date) ORDER BY month");
                            while ($row = $chart_res->fetch_assoc()) {
                                $chart_labels[] = date('M', mktime(0, 0, 0, $row['month'], 1));
                                $chart_data[] = $row['count'];
                            }
                        }

                        // 3. CLINIC STATUS LOGIC
                        if ($total_pts == 0) {
                            $status_msg = "No records found for " . $display_label . ".";
                            $status_color = "bg-slate-400";
                        } else {
                            $status_msg = "Showing $total_pts records for $display_label.";
                            $status_color = ($total_pts > 15) ? "bg-blue-400" : "bg-green-500";
                        }
                    ?>

                    <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
                        
                        <!-- HEADER & FILTERS -->
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-gray-800 tracking-tight italic">SynerQI Admin</h2>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Dashboard / <?php echo $display_label; ?></p>
                            </div>
                            
                            <form method="GET" class="flex flex-wrap items-center bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 gap-3">
                                <input type="hidden" name="page" value="dashboard">
                                
                                <div class="flex bg-gray-50 p-1 rounded-xl">
                                    <button type="submit" name="filter" value="day" class="px-3 py-1.5 text-[9px] font-black rounded-lg transition-all <?php echo $filter_type === 'day' ? 'bg-white text-primary shadow-sm' : 'text-gray-400'; ?>">DAY</button>
                                    <button type="submit" name="filter" value="month" class="px-3 py-1.5 text-[9px] font-black rounded-lg transition-all <?php echo $filter_type === 'month' ? 'bg-white text-primary shadow-sm' : 'text-gray-400'; ?>">MONTH</button>
                                    <button type="submit" name="filter" value="year" class="px-3 py-1.5 text-[9px] font-black rounded-lg transition-all <?php echo $filter_type === 'year' ? 'bg-white text-primary shadow-sm' : 'text-gray-400'; ?>">YEAR</button>
                                </div>

                                <div class="h-6 w-[1px] bg-gray-100 mx-1"></div>

                                <div class="flex items-center gap-2 pr-2">
                                    <i class="fas fa-calendar-alt text-primary text-xs opacity-40"></i>
                                    <input type="date" name="date_select" value="<?php echo $selected_date; ?>" onchange="this.form.submit()" 
                                        class="text-[10px] font-bold border-none focus:ring-0 bg-transparent cursor-pointer">
                                </div>
                            </form>
                        </div>

                        <!-- MAIN KPI GRID -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Total Appointments -->
                            <div class="bg-primary p-6 rounded-3xl shadow-lg shadow-primary/20 text-white relative overflow-hidden flex flex-col justify-between">
                                <div class="relative z-10">
                                    <p class="text-[10px] font-bold uppercase opacity-80 tracking-widest">Total Appointments</p>
                                    <h3 class="text-4xl font-black mb-4"><?php echo number_format($total_pts); ?></h3>
                                </div>
                                <div class="relative z-10 space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-bold opacity-80">
                                        <span>Fill Rate</span>
                                        <span><?php echo min(100, number_format(($total_pts/30)*100, 2)); ?>%</span>
                                    </div>
                                    <div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-white h-full rounded-full" style="width: <?php echo min(100, ($total_pts/30)*100); ?>%"></div>
                                    </div>
                                </div>
                                <i class="fas fa-calendar-check absolute right-4 top-8 opacity-10 text-6xl"></i>
                            </div>

                            <!-- Top Services -->
                            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Top Services</h4>
                                    <i class="fas fa-chart-pie text-primary text-xs opacity-40"></i>
                                </div>
                                <div class="space-y-4">
                                    <?php 
                                    $rank = 1;
                                    $icons = [1 => 'fa-crown text-yellow-500', 2 => 'fa-medal text-slate-400', 3 => 'fa-medal text-amber-600'];
                                    if (!empty($top_3_services)):
                                        foreach($top_3_services as $name => $count): 
                                            $pct = ($grand_total_services > 0) ? ($count / $grand_total_services) * 100 : 0;
                                    ?>
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                            <i class="fas <?php echo $icons[$rank]; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-end mb-1">
                                                <span class="text-[10px] font-black text-gray-700 uppercase truncate w-24"><?php echo $name; ?></span>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded"><?php echo $count; ?></span>
                                                    <span class="text-[10px] font-black text-primary"><?php echo number_format($pct, 2); ?>%</span>
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-50 h-1 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $rank++; endforeach; else: ?>
                                        <p class="text-[10px] text-gray-400 italic text-center py-4">No data for this selection.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Clinic Status -->
                            <div class="bg-gradient-to-br from-primary to-[#004d4d] p-6 rounded-3xl text-white shadow-xl relative overflow-hidden flex flex-col justify-center">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute h-full w-full rounded-full <?php echo $status_color; ?> opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 <?php echo $status_color; ?>"></span>
                                    </div>
                                    <p class="text-[9px] font-black uppercase tracking-widest">Live Status</p>
                                </div>
                                <p class="text-xs italic opacity-95">"<?php echo $status_msg; ?>"</p>
                                <div class="mt-4 pt-2 border-t border-white/10 text-[8px] opacity-50 flex justify-between font-black uppercase">
                                    <span>System Sync</span>
                                    <span><?php echo date('h:i A'); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT ROW -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Traffic Chart -->
                                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                                    <h4 class="text-xs font-black text-gray-700 uppercase tracking-widest mb-6">Patient Traffic Trend</h4>
                                    <div class="h-64"><canvas id="trendChart"></canvas></div>
                                </div>

                                <!-- RECENT ACTIVITY TABLE -->
                                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                                    <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-widest">Recently Added Patients</h4>
                                        <a href="?page=history" class="text-[10px] font-black text-primary hover:underline">VIEW FULL ARCHIVE</a>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead class="bg-gray-50/50">
                                                <tr class="text-[10px] text-gray-400 uppercase">
                                                    <th class="px-6 py-4 font-black">ID</th>
                                                    <th class="px-6 py-4 font-black">Patient Name</th>
                                                    <th class="px-6 py-4 font-black">Services</th>
                                                    <th class="px-6 py-4 text-right font-black">Date Logged</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-xs divide-y divide-gray-50">
                                                <?php if ($recent_pts && $recent_pts->num_rows > 0): while($row = $recent_pts->fetch_assoc()): ?>
                                                <tr class="hover:bg-gray-50/80 transition-colors">
                                                    <td class="px-6 py-4 font-mono text-gray-400">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                    <td class="px-6 py-4 font-bold text-gray-700"><?php echo $row['patient_name']; ?></td>
                                                    <td class="px-6 py-4">
                                                        <span class="text-[9px] font-black uppercase bg-teal-50 text-primary px-2 py-1 rounded-md border border-teal-100">
                                                            <?php echo $row['services']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right text-gray-400 font-medium">
                                                        <?php echo date('M d, Y', strtotime($row['date'])); ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; else: ?>
                                                <tr><td colspan="4" class="px-6 py-12 text-center text-[10px] text-gray-400 font-black uppercase italic">No records available</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- SIDEBAR: INVENTORY & ALERTS -->
                            <div class="space-y-6">
                                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                                    <div class="flex justify-between items-center mb-6">
                                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-widest">Inventory Watchlist</h4>
                                        <div class="flex items-center gap-2">
                                            <button onclick="openInventorySettings()" class="text-gray-300 hover:text-primary transition-colors">
                                                <i class="fas fa-pen-to-square text-[10px]"></i>
                                            </button>
                                            <i class="fas fa-bell text-red-400 text-xs animate-pulse"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <!-- LOW STOCK (RED) -->
                                        <div class="flex justify-between items-center p-4 bg-red-50 rounded-2xl border border-red-100">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-800">Sterile Gauze</span>
                                                <span class="text-[8px] font-black text-red-500 uppercase tracking-tighter">Low Stock</span>
                                            </div>
                                            <span class="text-sm font-black text-red-600">12 PCS</span>
                                        </div>

                                        <!-- AVERAGE STOCK (ORANGE) -->
                                        <div class="flex justify-between items-center p-4 bg-orange-50 rounded-2xl border border-orange-100">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-800">Alcohol (70%)</span>
                                                <span class="text-[8px] font-black text-orange-500 uppercase tracking-tighter">Average</span>
                                            </div>
                                            <span class="text-sm font-black text-orange-600">45 BTL</span>
                                        </div>

                                        <!-- FULL STOCK (GREEN) -->
                                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-2xl border border-green-100">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-800">Disposable Masks</span>
                                                <span class="text-[8px] font-black text-green-500 uppercase tracking-tighter">Full Stock</span>
                                            </div>
                                            <span class="text-sm font-black text-green-600">120 BOX</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('trendChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode(array_reverse($chart_labels)); ?>,
                                datasets: [{
                                    data: <?php echo json_encode(array_reverse($chart_data)); ?>,
                                    borderColor: '#008080',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#fff',
                                    fill: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 } } },
                                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                                }
                            }
                        });
                    });

                    function openInventorySettings() {
                        console.log("Global inventory edit clicked");
                    }
                    </script>

                    <?php } ?>
                    <!-- END HERE -->

                <!-- ONLINE APPOINTMENTS -->
                <?php elseif ($current_page === 'online'): ?>
                    
                    <?php
                        $online_calendar_data = [];
                        foreach ($patients as $p) {
                            $date_val = $p['date'] ?? null;
                            
                            // Check for both 'userName' and 'patient_name' depending on how it was inserted
                            $name_val = !empty($p['patient_name']) ? $p['patient_name'] : (!empty($p['userName']) ? $p['userName'] : 'Unknown');
                            $doctor_val = !empty($p['assigned_doctor']) ? $p['assigned_doctor'] : 'N/A';

                            if ($date_val) {
                                $parsedDate = date('Y-m-d', strtotime($date_val));
                                $parsedTime = !empty($p['bookingTime']) ? $p['bookingTime'] : date('h:i A', strtotime($date_val));
                                
                                if (!isset($online_calendar_data[$parsedDate])) {
                                    $online_calendar_data[$parsedDate] = [];
                                }
                                $online_calendar_data[$parsedDate][] = [
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
                                    <span id="onlineMonthYearDisplay" class="font-medium text-gray-700 w-32 text-center"></span>
                                    <button onclick="nextMonth()" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-2 text-center font-medium text-gray-400 text-sm mb-2">
                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="onlineCalendarDays" class="grid grid-cols-7 gap-2 text-center text-sm">
                                </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Scheduled Patients</h3>
                            <p id="onlineSelectedDateDisplay" class="text-sm text-gray-400 border-b border-gray-100 pb-3 mb-4">Select a date to view appointments.</p>
                            
                            <div id="onlineAppointmentsList" class="space-y-3 overflow-y-auto flex-1 max-h-[300px] pr-2">
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
                                                <input type="text" id="onlineSearchInput" onkeyup="filterHistoryTable()"
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

                                    <tbody id="onlinePatientTableBody">
                                        <?php foreach ($patients as $p): ?>
                                        <tr class="patient-row" data-patient-id="<?php echo $p['id']; ?>"
                                            style="<?php echo ($p['status'] === 'Confirmed') ? 'background-color: #fff9db !important; border-left: 4px solid #f59e0b; cursor: not-allowed;' : ''; ?>"

                                            <?php if (($p['status'] ?? 'Pending') !== 'Confirmed'): ?>
                                            onclick='editPatient(
                                                "SYNERQI-<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?>",
                                                <?php echo json_encode($p['patient_name'] ?? 'Unknown'); ?>,
                                                <?php echo json_encode($p['userEmail'] ?? ''); ?>,
                                                <?php echo json_encode(isset($p['date']) ? date('Y-m-d\TH:i', strtotime($p['date'])) : ''); ?>,
                                                <?php echo json_encode($p['telephone'] ?? 'N/A'); ?>,
                                                <?php echo json_encode($p['assigned_doctor'] ?? ''); ?>,
                                                <?php echo json_encode($p['vitals'] ?? ''); ?>,
                                                <?php echo json_encode($p['concern'] ?? ''); ?>,
                                                <?php echo ($p['status'] === 'Confirmed') ? 1 : 0; ?>
                                            )'
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
                                                            <button type="button" 
                                                                    onclick="event.stopPropagation(); openDoneModal(<?php echo $p['id']; ?>)" 
                                                                    style="color: #D4AF37; background: none; border: none; cursor: pointer; padding: 4px;" 
                                                                    title="Appointment Done">
                                                                <i class="fas fa-check-double" style="font-size: 1.15rem;"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" style="margin: 0;">
                                                            <input type="hidden" name="action" value="confirm_patient">
                                                            <input type="hidden" name="db_id" value="<?php echo $p['id']; ?>">
                                                            <button type="button" 
                                                                    class="confirm-appointment-btn"
                                                                    data-id="<?php echo $p['id']; ?>"
                                                                    data-name="<?php echo htmlspecialchars($p['patient_name'] ?? ''); ?>"
                                                                    data-email="<?php echo htmlspecialchars($p['userEmail'] ?? ''); ?>"
                                                                    data-date="<?php echo htmlspecialchars(isset($p['date']) ? date('Y-m-d\TH:i', strtotime($p['date'])) : ''); ?>"
                                                                    data-phone="<?php echo htmlspecialchars(!empty($p['telephone']) ? $p['telephone'] : (!empty($p['userPhone']) ? $p['userPhone'] : '')); ?>"
                                                                    data-doctor="<?php echo htmlspecialchars($p['assigned_doctor'] ?? ''); ?>"
                                                                    data-vitals="<?php echo htmlspecialchars($p['vitals'] ?? ''); ?>"
                                                                    data-concern="<?php echo htmlspecialchars($p['concern'] ?? ''); ?>"
                                                                    onclick="event.stopPropagation(); handleConfirmClick(this);"
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
            </div>

            <!-- HISTORY APPOINTMENTS -->
            <?php elseif ($current_page === 'history'): ?>

            <?php
                $history_calendar_data = [];

                foreach ($patients as $p) {
                    $date_val = $p['date'] ?? null;

                    $name_val = !empty($p['patient_name'])
                        ? $p['patient_name']
                        : (!empty($p['userName']) ? $p['userName'] : 'Unknown');

                    $doctor_val = !empty($p['assigned_doctor'])
                        ? $p['assigned_doctor']
                        : 'N/A';

                    if ($date_val) {

                        $parsedDate = date('Y-m-d', strtotime($date_val));

                        $parsedTime = !empty($p['bookingTime'])
                            ? $p['bookingTime']
                            : date('h:i A', strtotime($date_val));

                        if (!isset($history_calendar_data[$parsedDate])) {
                            $history_calendar_data[$parsedDate] = [];
                        }

                        $history_calendar_data[$parsedDate][] = [
                            'name' => $name_val,
                            'time' => $parsedTime,
                            'doctor' => $doctor_val
                        ];
                    }
                }
            ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                <!-- CALENDAR -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <div class="flex justify-between items-center mb-5">

                        <h3 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-history text-primary"></i>
                            Past Appointments
                        </h3>

                        <div class="flex items-center gap-3">

                            <button onclick="prevMonth()"
                                class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 transition text-gray-600">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <span id="historyMonthYearDisplay"
                                class="font-semibold text-gray-700 w-40 text-center">
                            </span>

                            <button onclick="nextMonth()"
                                class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 transition text-gray-600">
                                <i class="fas fa-chevron-right"></i>
                            </button>

                        </div>
                    </div>

                    <div class="grid grid-cols-7 gap-2 text-center text-sm font-medium text-gray-400 mb-3">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>

                    <div id="historyCalendarDays"
                        class="grid grid-cols-7 gap-2 text-center text-sm">
                    </div>

                </div>

                <!-- SIDE PANEL -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">

                    <h3 class="text-xl font-semibold text-gray-700 mb-2">
                        Past Patient Records
                    </h3>

                    <p id="historySelectedDateDisplay"
                        class="text-sm text-gray-400 border-b border-gray-100 pb-3 mb-4">
                        Select a date to view records.
                    </p>

                    <div id="historyAppointmentsList"
                        class="space-y-3 overflow-y-auto flex-1 max-h-[320px] pr-2">

                        <p class="text-sm text-gray-400 italic text-center mt-6">
                            No date selected.
                        </p>

                    </div>

                </div>
            </div>

            <!-- MASTERLIST -->
            <div class="masterlist-wrapper">

                <div class="masterlist-header">

                    <div style="display:flex; align-items:center; gap:14px;">

                        <h3 class="masterlist-title" style="margin:0;">
                            Online Appointment Records
                        </h3>

                        <div class="px-3 py-1 rounded-full bg-teal-50 text-primary text-xs font-semibold border border-teal-100">
                            <?php echo count($patients); ?> Total Records
                        </div>

                    </div>

                    <div class="masterlist-tools">

                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>

                            <input type="text"
                                id="historySearchInput"
                                onkeyup="filterOnlineTable()"
                                placeholder="Search records..."
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

                    <tbody id="historyPatientTableBody">

                        <?php foreach ($patients as $p): ?>

                        <tr class="patient-row cursor-pointer hover:bg-gray-50 transition"
                            onclick='viewPatient(
                            "ARCHIVE-<?php echo str_pad($p["arc_id"], 4, "0", STR_PAD_LEFT); ?>",
                            <?php echo json_encode($p["patient_name"] ?? "Unknown"); ?>,
                            <?php echo json_encode($p["userEmail"] ?? ""); ?>,
                            <?php echo json_encode(isset($p["date"]) ? date("M d, Y - h:i A", strtotime($p["date"])) : ""); ?>,
                            <?php echo json_encode($p["telephone"] ?? "N/A"); ?>,
                            <?php echo json_encode($p["assigned_doctor"] ?? ""); ?>,
                            <?php echo json_encode($p["vitals"] ?? ""); ?>,
                            <?php echo json_encode($p["concern"] ?? ""); ?>
                            )'
                        >

                            <!-- ID -->
                            <td class="id-cell">
                                ARCHIVE-<?php echo str_pad($p['arc_id'], 4, '0', STR_PAD_LEFT); ?>
                            </td>

                            <!-- NAME -->
                            <td class="name-cell">
                                <?php
                                    echo htmlspecialchars(
                                        !empty($p['patient_name'])
                                        ? $p['patient_name']
                                        : (!empty($p['userName']) ? $p['userName'] : 'Unknown')
                                    );
                                ?>
                            </td>

                            <!-- EMAIL -->
                            <td>
                                <?php echo htmlspecialchars($p['userEmail'] ?? 'N/A'); ?>
                            </td>

                            <!-- CONTACT -->
                            <td>
                                <?php
                                    echo htmlspecialchars(
                                        !empty($p['telephone'])
                                        ? $p['telephone']
                                        : (!empty($p['userPhone']) ? $p['userPhone'] : 'N/A')
                                    );
                                ?>
                            </td>

                            <!-- SERVICE -->
                            <td>
                                <span class="service-badge">
                                    <?php
                                        echo htmlspecialchars(
                                            !empty($p['services'])
                                            ? $p['services']
                                            : (!empty($p['serviceSelect']) ? $p['serviceSelect'] : 'Consultation')
                                        );
                                    ?>
                                </span>
                            </td>

                            <!-- DOCTOR -->
                            <td>
                                <div class="doctor-cell">
                                    <?php echo htmlspecialchars($p['assigned_doctor'] ?? 'Not Assigned'); ?>
                                </div>
                            </td>

                            <!-- SCHEDULE -->
                            <td>

                                <div class="date-main">
                                    <?php echo isset($p['date']) ? date('M d, Y', strtotime($p['date'])) : ''; ?>
                                </div>

                                <div class="time-sub">
                                    <?php echo isset($p['date']) ? date('h:i A', strtotime($p['date'])) : ''; ?>
                                </div>

                            </td>

                            <!-- AMOUNT -->
                            <td class="amount-cell">
                                ₱<?php echo number_format($p['totalAmount'] ?? 0); ?>
                            </td>

                            <!-- ACTION -->
                            <td>

                                <div class="action-wrap"
                                    style="display:flex; align-items:center; justify-content:center;">

                                    <form method="POST" style="margin:0;">

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

            <?php endif; ?>
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
                    <h2 class="text-2xl font-bold" style="color: #008080;">Edit Patient</h2>
                    <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>

                <form action="webadmin.php" method="POST" id="editPatientForm">
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
                                        required
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
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white"
                                    >
                                        <!-- Default empty option -->
                                        <option value="">Select Doctor</option>

                                        <?php foreach($doctors as $doc): ?>
                                            <option value="<?php echo htmlspecialchars($doc); ?>">
                                                <?php echo htmlspecialchars($doc); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- BUTTONS -->
                                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                    <button 
                                        type="submit"
                                        style="background-color: #008080;"
                                        class="flex-1 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition"
                                    >
                                        Save Changes
                                    </button>

                                    <button 
                                        type="button"
                                        data-id="<?php echo $id_display; ?>" 
                                        data-doctor="<?php echo $p['assigned_doctor']; ?>"
                                        data-vitals="<?php echo $p['vitals']; ?>"
                                        data-concern="<?php echo $p['concern']; ?>"
                                        onclick="handleConfirmClick(this)"
                                        style="background-color: #D4AF37;"
                                        class="flex-1 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition shadow-sm"
                                    >
                                        Confirm Appointment
                                    </button>
                                </div>

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

        <!-- VIEW PATIENT APPOINTMENT -->
         <div id="viewModal" class="modal fixed inset-0 z-50 overflow-visible bg-black/50 items-center justify-center hidden">
            <div class="bg-white rounded-2xl p-6 w-full max-w-5xl shadow-2xl relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Appointment Details</h2>
                    <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>

                <div class="flex flex-col md:flex-row overflow-hidden rounded-2xl border border-gray-200">

                    <div class="w-full md:w-3/5 bg-white p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                                <input type="text" id="view_patient_id" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 outline-none text-sm cursor-default">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name</label>
                                <input type="text" id="view_name" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 outline-none text-sm cursor-default">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="view_email" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 outline-none text-sm cursor-default">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
                                <input type="text" id="view_phone" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 outline-none text-sm cursor-default">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                                <input type="text" id="view_date" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 outline-none text-sm cursor-default">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Doctor</label>
                                <input type="text" id="view_doctor" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 outline-none text-sm cursor-default">
                            </div>

                            <button onclick="closeModal('viewModal')" 
                                    class="w-full text-white py-3 rounded-xl font-semibold transition mt-6"
                                    style="background-color: #008080; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#32a79d'"
                                    onmouseout="this.style.backgroundColor='#008080'">
                                Close View
                            </button>
                        </div>
                    </div>

                    <div class="w-full md:w-2/5 bg-gray-50 border-l border-gray-200 p-6">
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-800">Patient Notes</h3>
                            <p class="text-sm text-gray-500">Recorded medical observations.</p>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patient Vitals</label>
                                <textarea id="view_patient_vitals" readonly rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 outline-none text-sm resize-none cursor-default"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patient Concern</label>
                                <textarea id="view_patient_concern" readonly rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 outline-none text-sm resize-none cursor-default"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="script/adminscript.js" type="text/javascript"></script>
        <script>
        <?php if ($current_page === 'online'): ?>

            // =========================================
            // ONLINE CALENDAR VARIABLES
            // =========================================

            const onlineCalendarEvents =
                <?php echo json_encode($online_calendar_data); ?>;

            document.addEventListener('DOMContentLoaded', function () {

                // Load Online Calendar
                if (document.getElementById('onlineCalendarDays')) {
                    loadOnlineCalendar();
                }

            });

        <?php endif; ?>

        <?php if ($current_page === 'history'): ?>

            // =========================================
            // HISTORY CALENDAR VARIABLES
            // =========================================

            const historyCalendarEvents =
                <?php echo json_encode($history_calendar_data); ?>;

            document.addEventListener('DOMContentLoaded', function () {

                // Load History Calendar
                if (document.getElementById('historyCalendarDays')) {
                    loadHistoryCalendar();
                }

            });

        <?php endif; ?>
        </script>
    <?php endif; ?>


    <!-- CONFIRMATION POP UP -->
    <div id="doneConfirmModal" class="delete-overlay"> 
        <div class="delete-modal-box">
            <div class="mb-4 text-teal-500">
                <i class="fas fa-check-circle text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Mark as Completed?</h3>
            <p class="text-gray-500 mb-6 text-sm">This keeps the patient in the list but updates their status.</p>
            
            <form action="webadmin.php" method="POST">
                <input type="hidden" name="action" value="done_appointment">
                <input type="hidden" name="db_id" id="done_db_id"> 
                
                <div class="flex justify-center gap-3">
                    <button type="button" onclick="closeDoneModal()" class="btn-cancel">No</button>
                    <button type="submit" class="bg-teal-600 text-white px-8 py-2 rounded-lg font-medium hover:bg-teal-700">
                        Yes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CONFIRMATION VALIDATION POP UP -->
    <div id="validationNoticeModal" class="notice-overlay" style="display: none;">
        <div class="notice-box">
            <div class="notice-header">
                <span class="material-icons-round notice-icon" style="color: #f59e0b;">assignment_late</span>
                <h4 id="noticeTitle">Information Required</h4>
            </div>
            <div class="notice-body">
                <p id="noticeMessage">Please fill out the missing medical details before proceeding.</p>
            </div>
            <div class="notice-footer">
                <button type="button" id="noticeConfirmBtn" class="btn-confirm">Go to Field</button>
            </div>
        </div>
    </div>

    <!-- DELETE POP UP -->
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
    <script src="script/dbrelated.js" type="text/javascript"></script>
</body>
</html>
