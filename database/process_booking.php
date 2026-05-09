<?php
// 1. DATABASE CONNECTION
$conn = new mysqli('127.0.0.1', 'root', '', 'synerqi_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = false;
$generated_id = 0;

// 2. CAPTURE DATA & ALIGN WITH WEBADMIN VARIABLES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userName'])) {
    $patient_name    = $conn->real_escape_string($_POST['userName']);
    $userEmail       = $conn->real_escape_string($_POST['userEmail']);
    $telephone       = $conn->real_escape_string($_POST['userPhone']);
    $bookingDate     = $_POST['date']; 
    $bookingTime     = $_POST['bookingTime'];
    $services        = $conn->real_escape_string($_POST['selectedServicesList']); 
    $totalAmount     = intval($_POST['totalAmount']);
    
    // New Fields
    $vitals  = $conn->real_escape_string($_POST['patient_vitals']);
    $concern = $conn->real_escape_string($_POST['patient_concern']);

    // Logic to convert selection to database DATETIME format
    $time = ($bookingTime == "Morning (9AM - 12PM)") ? "09:00:00" : "13:00:00";
    $finalDateTime = $bookingDate . " " . $time;

    // 3. INSERT INTO online_appointments
    // Added patient_vitals and patient_concern to the column list and values
    $sql = "INSERT INTO online_appointments 
            (patient_name, userEmail, telephone, services, assigned_doctor, date, totalAmount, status, vitals, concern)
            VALUES
            ('$patient_name', '$userEmail', '$telephone', '$services', 'Pending Assignment', '$finalDateTime', '$totalAmount', 'Pending', '$vitals', '$concern')";

    if ($conn->query($sql)) {
        $success = true;
        // 4. DETECT THE NEWLY GENERATED ID
        $generated_id = $conn->insert_id; 
    } else {
        // Optional: Error handling
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - SynerQi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="../design/style.css">
    <link rel="stylesheet" href="../design/homepage.css">
    <link rel="stylesheet" href="../design/booking.css">
</head>
<body>

    <header>
        <div class="logo">
            <img src="../images/synerqi header logo.png" alt="SynerQi Logo">
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <div class="thank-you-container">
        <div class="thank-you-card">
            <?php if($success): ?>
                <span class="material-icons-round" style="color: #008080; font-size: 80px;">check_circle</span>
                <h1>Booking Confirmed</h1>
                
                <div class="schedule-text">
                    Reference Number: <br>
                    <span style="font-size: 1.5rem; color: #008080;">SYNERQI-<?php echo str_pad($generated_id, 4, '0', STR_PAD_LEFT); ?></span>
                </div>

                <p>We've received your request, <strong><?php echo htmlspecialchars($patient_name); ?></strong>!</p>
                
                <table class="transaction-table">
                    <thead><tr><th colspan="2">Transaction Details</th></tr></thead>
                    <tbody>
                        <tr><td>Email</td><td><?php echo htmlspecialchars($userEmail); ?></td></tr>
                        <tr><td>Phone</td><td><?php echo htmlspecialchars($telephone); ?></td></tr>
                        <tr><td>Schedule</td><td><?php echo date("M d, Y", strtotime($bookingDate)); ?> (<?php echo $bookingTime; ?>)</td></tr>
                        
                        <tr>
                            <td>Treatments</td>
                            <td><?php echo htmlspecialchars($services); ?></td>
                        </tr>
                        <tr>
                            <td>Vitals</td>
                            <td><?php echo !empty($vitals) ? nl2br(htmlspecialchars($vitals)) : '<em>Not provided</em>'; ?></td>
                        </tr>
                        <tr>
                            <td>Concern</td>
                            <td><?php echo !empty($concern) ? nl2br(htmlspecialchars($concern)) : '<em>Not provided</em>'; ?></td>
                        </tr>
                        <tr class="total-row"><td>Total Due</td><td>₱<?php echo number_format($totalAmount); ?></td></tr>
                    </tbody>
                </table>
                
                <div class="button-group">
                    <a href="../appointmentstatus.php" class="btn-status">Check Status</a>
                    <a href="../index.php" class="btn-return-home">Return to Home</a>
                </div>

            <?php else: ?>
                <span class="material-icons-round" style="color: #ff5252; font-size: 80px;">error</span>
                <h1>Booking Failed</h1>
                <p>There was an error processing your appointment. Please try again.</p>
                <a href="../index.php" class="btn-return-home">Try Again</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
