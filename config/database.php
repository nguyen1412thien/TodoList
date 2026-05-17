<?php
    $host     = getenv('DB_HOST')     ?: 'todolist-todolistby14dec.c.aivencloud.com';
    $port     = getenv('DB_PORT')     ?: 13680;
    $username = getenv('DB_USER')     ?: 'coder';
    $password = getenv('DB_PASSWORD') ?: '123456789';
    $database = getenv('DB_NAME')     ?: 'todolist';
    $ca_path  = __DIR__ . '/ca.pem';

    // Khởi tạo kết nối với SSL
    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, $ca_path, NULL, NULL);

    // Kết nối tới database
    if (!mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
        die(json_encode(["error" => "DB Connection failed: " . mysqli_connect_error()]));
    }
?>