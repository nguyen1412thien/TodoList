<?php

header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Database + Auth
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../middlewares/auth.php";
require_once __DIR__ . "/../../app/controllers/TodoController.php";

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
| Controller
|--------------------------------------------------------------------------
*/

$controller = new TodoController(
    $conn,
    $current_user
);

$id = $data["id"] ?? 0;

$result = $controller->destroy(
    $current_user->user_id,
    (int) $id
);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

http_response_code(
    $result["code"] ??
    ($result["success"] ? 200 : 400)
);

echo json_encode(

    $result["success"]

    ? [
        "message" => $result["message"]
    ]

    : [
        "error" => $result["error"]
    ]

);
