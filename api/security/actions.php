<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    require_once __DIR__ . "/../../config/database.php";
    require_once __DIR__ . "/../../app/controllers/SecurityController.php";

    // 1. Xác thực Token
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Unauthorized", 401);
    }

    $decoded = JWT::decode($matches[1], new Key("thien_jwt_secret_key_for_todolist_project", 'HS256'));
    $user_id = $decoded->user_id;

    $controller = new SecurityController($conn, $user_id);
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'change_password':
            $result = $controller->changePassword($data['old_password'], $data['new_password']);
            break;
        case 'update_email':
            $result = $controller->updateContactInfo('email', $data['email']);
            break;
        case 'update_phone':
            $result = $controller->updateContactInfo('phone', $data['phone']);
            break;
        case 'lock_account':
            $result = $controller->lockAccount();
            break;
        case 'delete_account':
            $result = $controller->deleteAccount();
            break;
        case 'get_history':
            $result = $controller->getLoginHistory($conn);
            break;
        case 'get_profile':
            $result = $controller->getProfile();
            break;
        default:
            throw new Exception("Invalid action", 400);
    }

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
