<?php

require_once __DIR__ . "/../models/User.php";

class AdminController
{
    private $userModel;
    private $current_user;

    public function __construct($conn, $current_user = null)
    {
        $this->userModel = new User($conn);
        $this->current_user = $current_user;
    }

    // Kiểm tra quyền Admin
    private function checkAdmin()
    {
        if (!$this->current_user || $this->current_user['role'] !== 'admin') {
            return false;
        }
        return true;
    }

    // Lấy danh sách tất cả người dùng (Chỉ Admin)
    public function getAllUsers($conn): array
    {
        if (!$this->checkAdmin()) {
            return [
                "success" => false,
                "error" => "Unauthorized: Admin role required",
                "code" => 403
            ];
        }

        try {
            $sql = "SELECT id, name, username, email, role, created_at, avatar FROM users ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);
            
            if (!$result) {
                throw new Exception(mysqli_error($conn));
            }

            $users = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            return [
                "success" => true,
                "data" => $users
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => "Database error: " . $e->getMessage(),
                "code" => 500
            ];
        }
    }
}
