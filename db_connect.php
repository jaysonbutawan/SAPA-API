<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$mysqli = new mysqli("localhost", "coordinator_user", "StrongPassword123!", "sapa_app_db");

if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}
?>