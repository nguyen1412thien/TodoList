<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middlewares/auth.php";
require_once __DIR__ . "/../app/controllers/TodoController.php";

/** @var mysqli $conn */
/** @var object $current_user */

$controller = new TodoController($conn, $current_user);

$result = $controller->index($current_user->user_id);

http_response_code($result["code"] ?? ($result["success"] ? 200 : 500));

$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $current_user->user_id);
$stmt->execute();
$db_user = $stmt->get_result()->fetch_assoc();

if ($result["success"]) {
    echo json_encode([
        "user" => [
            "id" => $db_user["id"],
            "name" => $db_user["name"],
            "username" => $db_user["username"],
            "email" => $db_user["email"],
            "avatar" => $db_user["avatar"] ?? null,
            "created_at" => $db_user["created_at"]
        ],
        "todos" => $result["data"]
    ]);
} else {
    echo json_encode(["error" => $result["error"]]);
}
