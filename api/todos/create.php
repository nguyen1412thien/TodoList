<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

/*
|--------------------------------------------------------------------------
| Get JSON request body
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

if (!$data) {

    echo json_encode([
        "error" => "Invalid JSON"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Validate required fields
|--------------------------------------------------------------------------
*/

if (
    !isset($data["user_id"]) ||
    !isset($data["title"]) ||
    !isset($data["description"])
) {

    echo json_encode([
        "error" => "Missing required fields"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Get data
|--------------------------------------------------------------------------
*/

$user_id = $data["user_id"];
$title = $data["title"];
$description = $data["description"];

/*
|--------------------------------------------------------------------------
| SQL Query
|--------------------------------------------------------------------------
*/

$sql = "
INSERT INTO todos (
    user_id,
    title,
    description
)
VALUES (
    '$user_id',
    '$title',
    '$description'
)
";

/*
|--------------------------------------------------------------------------
| Execute query
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, $sql);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

if ($result) {

    echo json_encode([
        "message" => "Todo created successfully",
        "todo_id" => mysqli_insert_id($conn)
    ]);

} else {

    echo json_encode([
        "error" => mysqli_error($conn)
    ]);

}