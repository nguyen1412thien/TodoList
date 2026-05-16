<?php

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0); 

require_once __DIR__ . "/../../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    require_once __DIR__ . "/../../config/database.php";
    require_once __DIR__ . "/../../app/controllers/AdminController.php";

    // 1. Xác thực Token
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Unauthorized", 401);
    }

    $decoded = JWT::decode($matches[1], new Key("thien_jwt_secret_key_for_todolist_project", 'HS256'));
    $current_user = (array) $decoded;

    // 2. Lấy dữ liệu từ Request
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['target_user_id'])) {
        throw new Exception("Missing data", 400);
    }

    // 3. Xử lý khóa/mở khóa
    $controller = new AdminController($conn, $current_user);
    $result = $controller->toggleUserStatus($conn, (int)$data['target_user_id']);

    http_response_code($result["success"] ? 200 : ($result["code"] ?? 400));
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
