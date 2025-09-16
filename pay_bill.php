<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "root", "", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$bill_id     = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
$amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;

if ($bill_id <= 0 || $amount_paid <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid bill_id or amount_paid"]);
    exit;
}

try {
    $stmt = $mysqli->prepare("CALL PayBill(?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("id", $bill_id, $amount_paid);

    if ($stmt->execute()) {
        $status = "success";
        $message = "Payment processed successfully";

        // check result sets from procedure if any
        do {
            if ($result = $stmt->get_result()) {
                while ($row = $result->fetch_assoc()) {
                    if (isset($row['status'])) {
                        $status = $row['status'];
                    }
                    if (isset($row['message'])) {
                        $message = $row['message'];
                    }
                }
                $result->free();
            }
        } while ($stmt->more_results() && $stmt->next_result());

        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}

$mysqli->close();
?>
