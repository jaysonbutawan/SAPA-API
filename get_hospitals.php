<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// DB connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// SQL query: get all hospitals
$sql = "SELECT * FROM view_hospitals";
$result = $conn->query($sql);

$hospitals = [];
while ($row = $result->fetch_assoc()) {
    $hospitals[] = [
        "hospital_id"   => (int) $row["hospital_id"],
        "hospital_name" => $row["hospital_name"],
        "hospital_address" => $row["hospital_address"],
        "contact_info"   => $row["contact_info"],
        "descriptions"   => $row["descriptions"]
    ];
}

// Output JSON
echo json_encode($hospitals);

$conn->close();
?>
