<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Check if department_id is provided
if (!isset($_GET['department_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing department_id parameter"
    ]);
    exit;
}

$department_id = intval($_GET['department_id']);

$stmt = $conn->prepare("CALL Get_Slot_Dates_By_Department(?)");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

$dateSlots = [];
while ($row = $result->fetch_assoc()) {
    $dateSlots[] = [
        "slot_date_id" => (int)$row["slot_date_id"],
        "slot_date" => $row["slot_date"],
        "department_id" => (int)$row["department_id"]
    ];
}

echo json_encode($dateSlots);

$stmt->close();
$conn->close();
?>
