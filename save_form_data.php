<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

$ip = $_SERVER['REMOTE_ADDR'];
$dataDir = __DIR__ . '/form_data/';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$file = $dataDir . md5($ip) . '.json'; // Hash IP for filename

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if ($data) {
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file)) {
        echo file_get_contents($file);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>