<?php 
    // Tự động lấy biến môi trường từ Docker, nếu không có sẽ dùng mặc định
    $host     = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: "127.0.0.1";
    $port     = (int)($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);
    $username = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: "root";
    $password = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: "14122005";
    $database = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?: "todolist";
    
    // Ép kết nối TCP (không dùng Unix socket của XAMPP)
    // mysqli_connect($host, $user, $pass, $db, $port, $socket=null)
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    if (!$conn) {
        die(json_encode(["error" => "DB Connection failed: " . mysqli_connect_error()]));
    }
?>