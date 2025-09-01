<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password required"]);
    exit();
}

$stmt = $mysqli->prepare("CALL LoginUser(?, ?, @p_status, @p_message, @p_user_id)");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$stmt->close();

while ($mysqli->more_results() && $mysqli->next_result()) {;}

$result = $mysqli->query("SELECT @p_status AS status, @p_message AS message, @p_user_id AS user_id");
$row = $result->fetch_assoc();

if ($row['status'] === 'error') {
    echo json_encode(["status" => "error", "message" => $row['message']]);
    exit();
}

if (!password_verify($password, $row['message'])) {
    echo json_encode(["status" => "error", "message" => "Invalid password"]);
    exit();
}

$_SESSION['user_id'] = $row['user_id'];

echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "user_id" => $row['user_id']
]);
?>
