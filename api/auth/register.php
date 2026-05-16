<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../app/controllers/AuthController.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

try {
    $controller = new AuthController($conn);
    $result = $controller->register($data);
    
    http_response_code($result["code"] ?? ($result["success"] ? 201 : 400));
    echo json_encode($result["success"] ? ["message" => $result["message"]] : ["error" => $result["error"]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
