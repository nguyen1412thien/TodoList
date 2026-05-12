<?php

header("Content-Type: application/json");

require_once "../config/database.php";

$sql = "SELECT * FROM todos";

$result = mysqli_query($conn, $sql);

$todos = mysqli_fetch_all(
    $result,
    MYSQLI_ASSOC
);

echo json_encode($todos);