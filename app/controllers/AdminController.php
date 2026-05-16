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
            $sql = "SELECT id, name, username, email, role, status, created_at, avatar FROM users ORDER BY created_at DESC";
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

    // Cập nhật quyền người dùng
    public function updateUserRole($conn, int $target_user_id, string $new_role): array
    {
        if (!$this->checkAdmin()) {
            return ["success" => false, "error" => "Admin only", "code" => 403];
        }

        try {
            // 1. Kiểm tra người dùng mục tiêu
            $sql = "SELECT id, role FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $target_user_id);
            mysqli_stmt_execute($stmt);
            $target_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$target_user) {
                return ["success" => false, "error" => "User not found", "code" => 404];
            }

            // 2. Quy tắc bảo mật: 
            // - Nếu là Admin khác: Không được sửa.
            // - Nếu là chính mình: Được sửa (Tự hạ quyền).
            // - Nếu là User: Được sửa (Nâng quyền).
            if ($target_user['role'] === 'admin' && (int)$target_user['id'] !== (int)$this->current_user['user_id']) {
                return [
                    "success" => false, 
                    "error" => "Bạn không có quyền chỉnh sửa vai trò của một Admin khác!", 
                    "code" => 403
                ];
            }

            // 3. Thực hiện cập nhật
            $update_sql = "UPDATE users SET role = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $new_role, $target_user_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                return [
                    "success" => true,
                    "message" => "Cập nhật vai trò thành công"
                ];
            } else {
                throw new Exception(mysqli_error($conn));
            }

        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => "Server error: " . $e->getMessage(),
                "code" => 500
            ];
        }
    }

    // Khóa/Mở khóa người dùng
    public function toggleUserStatus($conn, int $target_user_id): array
    {
        if (!$this->checkAdmin()) {
            return ["success" => false, "error" => "Admin only", "code" => 403];
        }

        try {
            $sql = "SELECT id, role, status FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $target_user_id);
            mysqli_stmt_execute($stmt);
            $target_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$target_user) {
                return ["success" => false, "error" => "User not found", "code" => 404];
            }

            if ($target_user['role'] === 'admin' && (int)$target_user['id'] !== (int)$this->current_user['user_id']) {
                return ["success" => false, "error" => "Bạn không có quyền khóa Admin khác!", "code" => 403];
            }

            $new_status = ($target_user['status'] === 'locked') ? 'active' : 'locked';
            
            if ($this->userModel->updateStatus($target_user_id, $new_status)) {
                return ["success" => true, "message" => "Đã " . ($new_status === 'locked' ? "khóa" : "mở khóa") . " tài khoản"];
            }
            return ["success" => false, "error" => "Không thể cập nhật trạng thái", "code" => 500];

        } catch (Exception $e) {
            return ["success" => false, "error" => "Server error", "code" => 500];
        }
    }

    // Xóa người dùng (từ Admin)
    public function deleteUser($conn, int $target_user_id): array
    {
        if (!$this->checkAdmin()) {
            return ["success" => false, "error" => "Admin only", "code" => 403];
        }

        try {
            $sql = "SELECT id, role FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $target_user_id);
            mysqli_stmt_execute($stmt);
            $target_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$target_user) {
                return ["success" => false, "error" => "User not found", "code" => 404];
            }

            if ($target_user['role'] === 'admin' && (int)$target_user['id'] !== (int)$this->current_user['user_id']) {
                return ["success" => false, "error" => "Bạn không có quyền xóa Admin khác!", "code" => 403];
            }

            if ($this->userModel->deleteAccount($target_user_id)) {
                return ["success" => true, "message" => "Tài khoản đã bị xóa an toàn"];
            }
            return ["success" => false, "error" => "Không thể xóa tài khoản", "code" => 500];

        } catch (Exception $e) {
            return ["success" => false, "error" => "Server error", "code" => 500];
        }
    }
}
