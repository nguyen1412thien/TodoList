<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$data) {
    die(json_encode([
        "error" => "Invalid JSON"
    ]));
}

$id = $data["id"];
$title = $data["title"];
$description = $data["description"];
$status = $data["status"];
$priority = $data["priority"];

$sql = "
UPDATE todos
SET
    title = '$title',
    description = '$description',
    status = '$status',
    priority = '$priority'
WHERE id = '$id'
";

$result = mysqli_query($conn, $sql);

if ($result) {

    echo json_encode([
        "message" => "Todo updated"
    ]);

} else {

    echo json_encode([
        "error" => mysqli_error($conn)
    ]);

}