<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Database + Auth
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../middlewares/auth.php";
require_once __DIR__ . "/../../app/models/Todo.php";

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
    !isset($data["title"]) ||
    empty(trim($data["title"]))
) {

    echo json_encode([
        "error" => "Title is required"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Current authenticated user
|--------------------------------------------------------------------------
*/

$user_id = $current_user->user_id;

/*
|--------------------------------------------------------------------------
| Request data
|--------------------------------------------------------------------------
*/

$title = trim($data["title"]);
$description = $data["description"] ?? "";

/*
|--------------------------------------------------------------------------
| Insert todo
|--------------------------------------------------------------------------
*/

$todo = new Todo($conn); //todo model instance

$result = $todo->create(
    $user_id,
    $title,
    $description
);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

if ($result) {

    echo json_encode([
        "message" => "Todo created"
    ]);

} else {

    echo json_encode([
        "error" => mysqli_error($conn)
    ]);

}