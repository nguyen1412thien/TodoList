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

if ($result["success"]) {
    echo json_encode([
        "user" => [
            "id" => $current_user->user_id,
            "email" => $current_user->email
        ],
        "todos" => $result["data"]
    ]);
} else {
    echo json_encode(["error" => $result["error"]]);
}
