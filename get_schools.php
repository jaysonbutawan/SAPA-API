<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "sapa_app_db");

$user_id = $_POST['user_id'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($user_id) || empty($status)) {
    echo json_encode([]);
    exit;
}

$sql = "CALL GetUserSchools(?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $status);
$stmt->execute();
$result = $stmt->get_result();

$schools = [];
while ($row = $result->fetch_assoc()) {
    $schools[] = $row;
}

echo json_encode($schools);

