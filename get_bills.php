<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Parameters
$school_id = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

if ($school_id <= 0) {
    echo json_encode(["error" => "Invalid school_id"]);
    exit;
}

// Call stored procedure
$stmt = $conn->prepare("CALL GetBillsBySchool(?)");
$stmt->bind_param("i", $school_id);

$stmt->execute();
$result = $stmt->get_result();

$bills = [];
while ($row = $result->fetch_assoc()) {
    $bills[] = [
        "bill_id"        => (int) $row['bill_id'],
        "bill_reference" => $row['bill_reference'],
        "appointment_id" => (int) $row['appointment_id'],
        "total_amount"   => (float) $row['total_amount'],
        "status"         => $row['status'],
        "date_issued"    => $row['date_issued'],
        "paid_date"      => $row['paid_date']
    ];
}

// Return JSON
echo json_encode($bills);

$stmt->close();
$conn->close();
?>
