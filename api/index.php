<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middlewares/auth.php";

/** @var mysqli $conn */
/** @var object $current_user */

$user_id = $current_user->user_id;

$sql = "
SELECT * FROM todos
WHERE user_id = '$user_id'
";

$result = mysqli_query($conn, $sql);

$todos = mysqli_fetch_all(
    $result,
    MYSQLI_ASSOC
);

echo json_encode([
    "user" => [
        "id" => $current_user->user_id,
        "email" => $current_user->email
    ],
    "todos" => $todos
]);