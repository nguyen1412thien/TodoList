<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Database + JWT
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/*
|--------------------------------------------------------------------------
| Get JSON body
|--------------------------------------------------------------------------
*/

$data = json_decode(
    file_get_contents("php://input"),
    true
);

/*
|--------------------------------------------------------------------------
| Validate JSON
|--------------------------------------------------------------------------
*/

if (!$data || !is_array($data)) {

    echo json_encode([
        "error" => "Invalid JSON"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Validate fields
|--------------------------------------------------------------------------
*/

if (
    !isset($data["email"]) ||
    !isset($data["password"])
) {

    echo json_encode([
        "error" => "Email and password are required"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Get request data
|--------------------------------------------------------------------------
*/

$email = $data["email"];
$password = $data["password"];

/*
|--------------------------------------------------------------------------
| Find user by email
|--------------------------------------------------------------------------
*/

$sql = "
SELECT * FROM users
WHERE email = '$email'
";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| User not found
|--------------------------------------------------------------------------
*/

if (!$user) {

    echo json_encode([
        "error" => "User not found"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Verify password
|--------------------------------------------------------------------------
*/

$is_password_correct = password_verify(
    $password,
    $user["password"]
);

if (!$is_password_correct) {

    echo json_encode([
        "error" => "Invalid password"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| JWT Secret Key
|--------------------------------------------------------------------------
*/

$secret_key = "thien_jwt_secret_key_for_todolist_project";

/*
|--------------------------------------------------------------------------
| JWT Payload
|--------------------------------------------------------------------------
*/

$payload = [
    "user_id" => $user["id"],
    "username" => $user["username"],
    "email" => $user["email"],
    "iat" => time(),
    "exp" => time() + 3600
];

/*
|--------------------------------------------------------------------------
| Create JWT Token
|--------------------------------------------------------------------------
*/

$jwt = JWT::encode(
    $payload,
    $secret_key,
    'HS256'
);

/*
|--------------------------------------------------------------------------
| Success Response
|--------------------------------------------------------------------------
*/

echo json_encode([
    "message" => "Login successful",
    "token" => $jwt,
    "user" => [
        "id" => $user["id"],
        "username" => $user["username"],
        "email" => $user["email"]
    ]
]);