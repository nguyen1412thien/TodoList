<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    header("Content-Type: application/json");
    require_once __DIR__ . "/../../config/database.php";

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];
    /**Hash the password */
    $hashed_password = password_hash(
        $password, PASSWORD_DEFAULT
    );
    ///Insert user into database
    $sql = "
    INSERT INTO users (
        username, 
        email, 
        password
    )
    VALUES (
        '$username', 
        '$email', 
        '$hashed_password'
    )
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {

        echo json_encode([
            "message" => "User registered"
        ]);

    } else {

        echo json_encode([
            "error" => mysqli_error($conn)
        ]);

    }
    
