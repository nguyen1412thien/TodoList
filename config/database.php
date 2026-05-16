<?php 
    $host = "192.168.10.149";
    $username = "root";
    $password = "14122005";
    $database = "todolist";
    $conn = mysqli_connect($host, $username, $password, $database);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>