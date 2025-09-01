<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "sapa_app_db");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$school_id = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

if ($user_id <= 0 || $school_id <= 0) {
    echo json_encode([]);
    exit;
}

// Call stored procedure
$stmt = $conn->prepare("CALL getStudentsBySchoolAndUser(?, ?)");
$stmt->bind_param("ii", $school_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch results
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        "student_id"   => (int) $row['student_id'],
        "student_code" => $row['student_code'],
        "firstname"    => $row['firstname'],
        "lastname"     => $row['lastname'],
        "phone_number" => $row['phone_number'],
        "email"        => $row['email'],
        "sex"          => $row['sex'],
        "age"          => (int) $row['age'],
        "school_id"    => (int) $row['school_id']
    ];
}

echo json_encode($students);

$stmt->close();
$conn->close();
?>
