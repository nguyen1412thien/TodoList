<?php

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0); 

require_once __DIR__ . "/../../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    require_once __DIR__ . "/../../config/database.php";
    if (!isset($conn)) {
        throw new Exception("Database connection variable (\$conn) is not defined.");
    }

    require_once __DIR__ . "/../../app/controllers/AdminController.php";

    // Xác thực JWT
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "No token provided"]);
        exit;
    }

    $jwt = $matches[1];
    $decoded = JWT::decode($jwt, new Key("thien_jwt_secret_key_for_todolist_project", 'HS256'));
    $current_user = (array) $decoded;

    if (!in_array($current_user['role'], ['admin', 'superadmin'])) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Access denied: Admin only"]);
        exit;
    }

    $controller = new AdminController($conn, $current_user);
    $result = $controller->getAllUsers($conn);

    http_response_code($result["success"] ? 200 : 500);
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "System Error: " . $e->getMessage()
    ]);
}
