<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Check if date_slot_id is provided
if (!isset($_GET['date_slot_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing date_slot_id parameter"
    ]);
    exit;
}

$slot_date_id = intval($_GET['date_slot_id']);

$sql = "CALL FetchTimeSlotsWithBookingCount(?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_date_id);
$stmt->execute();
$result = $stmt->get_result();

$timeSlots = [];
while ($row = $result->fetch_assoc()) {
    $timeSlots[] = [
        "time_slot_id" => (int)$row["time_slot_id"],
        "slot_date_id" => (int)$row["slot_date_id"],
        "start_time" => $row["start_time"],
        "end_time" => $row["end_time"],
        "capacity" => (int)$row["capacity"],
        "booked_count" => (int)$row["booked_count"],
    
    ];
}

echo json_encode($timeSlots);

$stmt->close();
$conn->close();
?>
