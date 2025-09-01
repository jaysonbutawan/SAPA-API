<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "sapa_app_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$school_id      = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;
$hospital_id    = isset($_GET['hospital_id'])? intval($_GET['hospital_id']): null;
$department_id  = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$slot_date_id   = isset($_GET['slot_date_id']) ? intval($_GET['slot_date_id']) : null;
$time_slot_id   = isset($_GET['time_slot_id']) ? intval($_GET['time_slot_id']) : null;

if ($school_id <= 0) {
    echo json_encode(["error" => "Invalid school_id"]);
    exit;
}

$stmt = $conn->prepare("CALL Get_Booked_Students(?, ?, ?, ?,?)");
$stmt->bind_param("iiiii", $school_id, $hospital_id, $department_id, $slot_date_id, $time_slot_id);

$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        "student_id"       => (int) $row['student_id'],
        "firstname"        => $row['firstname'],
        "lastname"         => $row['lastname'],
        "section_name"  => $row['section_name'],
        "slot_date"        => $row['slot_date'],
        "start_time"       => $row['start_time'],
        "end_time"         => $row['end_time'],
        "appointment_status" => $row['appointment_status']
    ];
}

echo json_encode($bookings);

$stmt->close();
$conn->close();
?>
