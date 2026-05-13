<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data["id"];

$sql = "
DELETE FROM todos
WHERE id = '$id'
";

$result = mysqli_query($conn, $sql);

if ($result) {

    echo json_encode([
        "message" => "Todo deleted"
    ]);

} else {

    echo json_encode([
        "error" => mysqli_error($conn)
    ]);

}