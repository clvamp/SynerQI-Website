<?php
// 1. DATABASE CONNECTION
$conn = new mysqli('127.0.0.1', 'root', '', 'synerqi_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = false;

// 2. CAPTURE DATA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userName'])) {
    $userName      = $_POST['userName'];
    $userEmail     = $_POST['userEmail'];
    $userPhone     = $_POST['userPhone'];
    $bookingDate   = $_POST['date']; 
    $bookingTime   = $_POST['bookingTime'];
    $services      = $_POST['selectedServicesList']; // From hidden input
    $totalAmount   = $_POST['totalAmount'];           // From hidden input

    // 3. INSERT INTO DATABASE (Matches the SQL columns exactly)
    $sql = "INSERT INTO onsite_appointments (userName, userEmail, userPhone, serviceSelect, date, bookingTime, totalAmount) 
            VALUES ('$userName', '$userEmail', '$userPhone', '$services', '$bookingDate', '$bookingTime', '$totalAmount')";

    $success = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - SynerQi</title>
    <link rel="stylesheet" href="design/style.css">
    <link rel="stylesheet" href="design/homepage.css">
    <link rel="stylesheet" href="design/booking.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        .check-icon { color: #008080; font-size: 80px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/synerqi header logo.png" alt="SynerQi Logo">
            </a>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <div class="thank-you-container">
        <div class="thank-you-card">
            <?php if($success): ?>
                <span class="material-icons-round check-icon">check_circle</span>
                <h1>Booking Confirmed</h1>
                <p>We've received your request, <strong><?php echo htmlspecialchars($userName); ?></strong>!</p>
                
                <div class="schedule-text">
                    You are scheduled at <?php echo date("F j, Y", strtotime($bookingDate)); ?> at <?php echo htmlspecialchars($bookingTime); ?>
                </div>

                <table class="transaction-table">
                    <thead>
                        <tr><th colspan="2">Transaction Details</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Services</td><td><?php echo htmlspecialchars($services); ?></td></tr>
                        <tr><td>Phone</td><td><?php echo htmlspecialchars($userPhone); ?></td></tr>
                        <tr class="total-row"><td>Total Due</td><td>₱<?php echo number_format($totalAmount); ?></td></tr>
                    </tbody>
                </table>
            <?php else: ?>
                <span class="material-icons-round check-icon" style="color: #ff5252;">error</span>
                <h1>Something went wrong</h1>
                <p>We couldn't process your booking. Please try again.</p>
            <?php endif; ?>

            <br>
            <a href="index.php" class="btn-return-home">Return to Home</a>
        </div>
    </div>

</body>
</html>