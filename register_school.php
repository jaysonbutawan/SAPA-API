<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$school_name = $_POST['school_name'] ?? '';
$school_address = $_POST['school_address'] ?? '';
$contact_info = $_POST['contact_info'] ?? '';
$user_id = $_POST['user_id'] ?? '';

if (empty($school_name) || empty($school_address) || empty($user_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $mysqli->prepare("CALL AddSchool(?, ?, ?, ?)");
    $stmt->bind_param("sssi", $school_name, $school_address, $contact_info, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "School added and waiting for admin approval"]);
    } else {
        $errorMsg = $stmt->error ? $stmt->error : $mysqli->error;
        echo json_encode(["status" => "error", "message" => $errorMsg]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$mysqli->close();
?>
