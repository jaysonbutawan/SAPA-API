<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$student_id = $_POST['student_id'] ?? '';
$school_id  = $_POST['school_id'] ?? '';

if (empty($student_id) || empty($school_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $mysqli->prepare("CALL delete_student(?, ?)");
    $stmt->bind_param("ii", $student_id, $school_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Student deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No matching student found or deletion failed"]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error ?: $mysqli->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$mysqli->close();
?>
