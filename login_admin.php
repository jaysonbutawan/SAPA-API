<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sapa_app_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = $data["email"] ?? "";
$password = $data["password"] ?? "";

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email and password required"]);
    exit;
}

// Call procedure
$stmt = $conn->prepare("CALL LoginUser(?, ?, @p_status, @p_message, @p_user_id, @p_role_id)");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$stmt->close();

// Get OUT values
$result = $conn->query("SELECT @p_status AS status, @p_message AS message, @p_user_id AS user_id, @p_role_id AS role_id");
$row = $result->fetch_assoc();

echo json_encode([
    "success" => $row["status"] === "success",
    "message" => $row["message"],
    "user_id" => $row["user_id"],
    "role_id" => $row["role_id"]
]);

$conn->close();
?>
