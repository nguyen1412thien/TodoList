<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../middlewares/auth.php";
require_once __DIR__ . "/../../app/controllers/AdminController.php";

$current_user = (array)$decoded;
$controller = new AdminController($conn, $current_user);
$result = $controller->getDeletedUsers($conn);

http_response_code($result["code"] ?? ($result["success"] ? 200 : 500));
echo json_encode($result);
