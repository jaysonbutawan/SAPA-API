<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB Connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$school_id     = $data['school_id'] ?? null;
$hospital_id   = $data['hospital_id'] ?? null;
$department_id = $data['department_id'] ?? null;
$slot_date_id  = $data['slot_date_id'] ?? null;
$time_slot_id  = $data['time_slot_id'] ?? null;
$student_ids   = $data['student_ids'] ?? ""; // already CSV from Android

if (!$school_id || !$hospital_id || !$department_id || !$slot_date_id || !$time_slot_id || empty($student_ids)) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$sql = "CALL Cancel_Multiple_Students(?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiis", $school_id, $hospital_id, $department_id, $slot_date_id, $time_slot_id, $student_ids);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
