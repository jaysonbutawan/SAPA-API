<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$school_id        = isset($_POST['school_id']) ? intval($_POST['school_id']) : 0;
$hospital_id      = isset($_POST['hospital_id']) ? intval($_POST['hospital_id']) : 0;
$department_id    = isset($_POST['department_id']) ? intval($_POST['department_id']) : 0;
$slot_date_id     = isset($_POST['slot_date_id']) ? intval($_POST['slot_date_id']) : 0;
$time_slot_id     = isset($_POST['time_slot_id']) ? intval($_POST['time_slot_id']) : 0;
$student_ids_json = $_POST['student_ids'] ?? '[]';

$student_ids = json_decode($student_ids_json, true);

if (
    $school_id <= 0 || $hospital_id <= 0 || $department_id <= 0 ||
    $slot_date_id <= 0 || $time_slot_id <= 0 ||
    !is_array($student_ids) || empty($student_ids)
) {
    echo json_encode(["success" => false, "message" => "Invalid input values"]);
    exit;
}

$student_ids_json = json_encode(array_map('intval', $student_ids));

try {
    $stmt = $mysqli->prepare("CALL BookAppointmentWithStudents(?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param(
        "iiiiis",
        $school_id,
        $hospital_id,
        $department_id,
        $slot_date_id,
        $time_slot_id,
        $student_ids_json
    );

    if ($stmt->execute()) {
        $status  = null;
        $message = null;

        // fetch result sets
        do {
            if ($result = $stmt->get_result()) {
                while ($row = $result->fetch_assoc()) {
                    $status  = $row['status'] ?? null;
                    $message = $row['message'] ?? null;
                }
                $result->free();
            }
        } while ($stmt->more_results() && $stmt->next_result());

        echo json_encode([
            "success" => ($status === 'success'),
            "message" => $message ?? "No message returned"
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

$mysqli->close();
?>
