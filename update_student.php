<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$student_id     = $_POST['student_id'] ?? '';
$firstname      = $_POST['firstname'] ?? '';
$lastname       = $_POST['lastname'] ?? '';
$phone_number   = $_POST['phone_number'] ?? '';
$email          = $_POST['email'] ?? '';
$sex            = $_POST['sex'] ?? '';
$date_of_birth  = $_POST['date_of_birth'] ?? '';
$school_id      = $_POST['school_id'] ?? '';

if (empty($student_id) || empty($firstname) || empty($lastname) || empty($school_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $mysqli->prepare("CALL update_student(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $student_id, $firstname, $lastname, $phone_number, $email, $sex, $date_of_birth, $school_id);

    if ($stmt->execute()) {
        if ($stmt->errno) {
            echo json_encode([
                "status" => "error",
                "message" => $stmt->error
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "message" => "Student updated successfully"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error ?: $mysqli->error
        ]);
    }
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$mysqli->close();
?>
