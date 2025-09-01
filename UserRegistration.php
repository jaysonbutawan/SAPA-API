<?php
// Always return JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Standard response structure
$response = ["status" => "error", "message" => "Unknown error"];

// Connect to DB
$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");
if ($mysqli->connect_errno) {
    $response["message"] = "DB Connection failed: " . $mysqli->connect_error;
    echo json_encode($response);
    exit();
}

// Read POST safely
$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$role_id   = NULL; // Admin assigns later

// Validate input
if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
    $response["message"] = "All fields are required.";
    echo json_encode($response);
    exit();
}

// Optional: Password length validation (example: 5 chars only)
if (strlen($password) != 5) {
    $response["message"] = "Password must be exactly 5 characters.";
    echo json_encode($response);
    exit();
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Prepare and execute stored procedure
    $stmt = $mysqli->prepare("CALL RegisterUser(?, ?, ?, ?, ?, @p_status, @p_message)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $passwordHash, $role_id);
    $stmt->execute();

    // Clear any additional results
    while ($mysqli->more_results() && $mysqli->next_result()) {}

    // Fetch OUT parameters from procedure
    $result = $mysqli->query("SELECT @p_status AS status, @p_message AS message");
    if ($result) {
        $row = $result->fetch_assoc();
        $response["status"]  = $row["status"];
        $response["message"] = $row["message"];
    }

} catch (mysqli_sql_exception $e) {
    // Handle trigger violation (duplicate firstname+lastname)
    if (strpos($e->getMessage(), "SIGNAL") !== false || strpos($e->getMessage(), "already exists") !== false) {
        $response["status"]  = "error";
        $response["message"] = "A user with the same first name and last name already exists.";
    } else {
        $response["status"]  = "error";
        $response["message"] = "Registration failed: " . $e->getMessage();
    }
} catch (Exception $e) {
    $response["status"]  = "error";
    $response["message"] = "Registration failed: " . $e->getMessage();
}

// Close resources
if (isset($stmt) && $stmt) $stmt->close();
$mysqli->close();

// Output JSON
echo json_encode($response);
exit();
?>
