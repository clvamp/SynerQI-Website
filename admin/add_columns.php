<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'synerqi_db');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$result = $conn->query('DESCRIBE online_appointments');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . ' - ' . ($row['Default'] ? $row['Default'] : 'NO DEFAULT') . "\n";
    }
}
$conn->close();
?>