<?php 
    $host = getenv('DB_HOST') ?: "127.0.0.1";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASS') ?: "14122005";
    $database = getenv('DB_NAME') ?: "todolist";
    
    $conn = mysqli_connect($host, $username, $password, $database);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>