<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "sapa_app_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$school_id = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

if ($school_id <= 0) {
    echo json_encode(["error" => "Invalid school_id"]);
    exit;
}

$query = "SELECT GetFutureAppointmentsBySchool(?) AS booking_count";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $school_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Query execution failed"]);
    exit;
}

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "school_id" => $school_id,
    "booking_count" => (int) $data['booking_count']
]);

$stmt->close();
$conn->close();
?>
