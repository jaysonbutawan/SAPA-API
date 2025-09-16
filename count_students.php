<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// DB connection
$conn = new mysqli("localhost", "root", "", "sapa_app_db");
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

// Get parameters
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$schoolId = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

if ($userId <= 0 || $schoolId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid parameters.",
        "student_count" => 0
    ]);
    exit;
}

// Use FUNCTION instead of PROCEDURE
$query = "SELECT GetStudentCountByUserAndSchool(?, ?) AS student_count";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $schoolId);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Query execution failed.",
        "student_count" => 0
    ]);
    exit;
}

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode([
    "status" => "success", // <-- instead of "success" => true
    "message" => "Count retrieved successfully.",
    "student_count" => (int)$data['student_count']
]);


$stmt->close();
$conn->close();
?>
