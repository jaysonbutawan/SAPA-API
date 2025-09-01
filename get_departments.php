<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Check hospital_id param
if (!isset($_GET['hospital_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing hospital_id parameter"
    ]);
    exit;
}

$hospital_id = intval($_GET['hospital_id']);

// SQL query using your stored procedure or view
$sql = "CALL GetDepartmentsByHospital(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = [
        "department_id" => (int) $row["department_id"],
        "section_name" => $row["section_name"],
        "price_per_student" => (float) $row["price_per_student"],
        "hospital_id" => (int) $row["hospital_id"],
        "hospital_name" => $row["hospital_name"]
    ];
}

// Output JSON
echo json_encode($departments);

$stmt->close();
$conn->close();
?>
