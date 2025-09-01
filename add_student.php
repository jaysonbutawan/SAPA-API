<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$user_id        = $_POST['user_id'] ?? '';
$firstname      = $_POST['firstname'] ?? '';
$lastname       = $_POST['lastname'] ?? '';
$phone_number   = $_POST['phone_number'] ?? '';
$email          = $_POST['email'] ?? '';
$sex            = $_POST['sex'] ?? '';
$date_of_birth  = $_POST['date_of_birth'] ?? '';
$school_id      = $_POST['school_id'] ?? '';

if (empty($user_id) || empty($firstname) || empty($lastname) || empty($school_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $mysqli->prepare("CALL add_student(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $user_id, $firstname, $lastname, $phone_number, $email, $sex, $date_of_birth, $school_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Student added successfully"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error ?: $mysqli->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$mysqli->close();
?>
