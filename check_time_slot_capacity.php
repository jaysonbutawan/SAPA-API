<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");


$conn = new mysqli("localhost", "root", "", "sapa_app_db");


$time_slot_id = isset($_GET['time_slot_id']) ? intval($_GET['time_slot_id']) : 0;


if ($time_slot_id <= 0) {
echo json_encode(["success" => false, "message" => "Invalid time_slot_id"]);
exit;
}


$stmt = $conn->prepare("CALL getTimeSlotCapacity(?)");
$stmt->bind_param("i", $time_slot_id);
$stmt->execute();
$result = $stmt->get_result();


$response = [];
if ($row = $result->fetch_assoc()) {
$response = [
"success" => true,
"time_slot_id" => (int)$row['time_slot_id'],
"capacity" => (int)$row['capacity'],
"booked_count" => (int)$row['booked_count'],
"remaining_capacity" => (int)$row['remaining_capacity']
];
} else {
$response = ["success" => false, "message" => "Time slot not found"];
}


echo json_encode($response);


$stmt->close();
$conn->close();
?>