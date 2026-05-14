<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//secret key for
$secret_key = "thien_jwt_secret_key_for_todolist_project";

$headers = getallheaders();

//check authorization header
if (!isset($headers["Authorization"])) {
    echo json_encode([
        "error" => "Authorization header is required"
    ]);

    exit;
}

//get token from header
$auth_header = $headers["Authorization"];
$token = str_replace("Bearer ", "", $auth_header);

//verify JWT token
try {
    $decoded = JWT::decode($token, new Key($secret_key, "HS256"));
} catch (Exception $e) {
    echo json_encode([
        "error" => "Invalid token: " . $e->getMessage()
    ]);
    exit;
}

//current user 
$current_user = $decoded;
