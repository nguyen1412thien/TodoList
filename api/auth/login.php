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

$controller = new AuthController($conn);
$result = $controller->login($data);

http_response_code($result["code"] ?? ($result["success"] ? 200 : 400));

if ($result["success"]) {
    echo json_encode([
        "message" => $result["message"],
        "token" => $result["token"],
        "user" => $result["user"]
    ]);
} else {
    echo json_encode(["error" => $result["error"]]);
}
