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

    // Kiểm tra quyền Admin (bao gồm cả superadmin)
    private function checkAdmin()
    {
        if (!$this->current_user) return false;
        return in_array($this->current_user['role'], ['admin', 'superadmin']);
    }

    // Kiểm tra quyền Superadmin
    private function isSuperAdmin()
    {
        return $this->current_user && $this->current_user['role'] === 'superadmin';
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

            // 2. Superadmin có toàn quyền. Admin thường không được sửa Admin khác.
            $isSelf = (int)$target_user['id'] === (int)$this->current_user['user_id'];
            if (!$this->isSuperAdmin() && !$isSelf && in_array($target_user['role'], ['admin', 'superadmin'])) {
                return [
                    "success" => false, 
                    "error" => "Bạn không có quyền chỉnh sửa vai trò của Admin khác!", 
                    "code" => 403
                ];
            }

            // Superadmin không thể bị hạ xuống role thấp hơn bởi Admin thường
            if ($target_user['role'] === 'superadmin' && !$this->isSuperAdmin()) {
                return ["success" => false, "error" => "Không có quyền thao tác với Superadmin!", "code" => 403];
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

            // Superadmin không thể bị khóa bởi Admin thường
            if (in_array($target_user['role'], ['admin', 'superadmin'])
                && (int)$target_user['id'] !== (int)$this->current_user['user_id']
                && !$this->isSuperAdmin()) {
                return ["success" => false, "error" => "Bạn không có quyền khóa Admin khác!", "code" => 403];
            }

            if ($target_user['role'] === 'superadmin' && !$this->isSuperAdmin()) {
                return ["success" => false, "error" => "Không có quyền thao tác với Superadmin!", "code" => 403];
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

            if (in_array($target_user['role'], ['admin', 'superadmin'])
                && (int)$target_user['id'] !== (int)$this->current_user['user_id']
                && !$this->isSuperAdmin()) {
                return ["success" => false, "error" => "Bạn không có quyền xóa Admin khác!", "code" => 403];
            }

            if ($target_user['role'] === 'superadmin' && !$this->isSuperAdmin()) {
                return ["success" => false, "error" => "Không có quyền thao tác với Superadmin!", "code" => 403];
            }

            if ($this->userModel->deleteAccount($target_user_id)) {
                return ["success" => true, "message" => "Tài khoản đã bị xóa an toàn"];
            }
            return ["success" => false, "error" => "Không thể xóa tài khoản", "code" => 500];

        } catch (Exception $e) {
            return ["success" => false, "error" => "Server error", "code" => 500];
        }
    }
    // Lấy danh sách tài khoản đã xóa (Chỉ Superadmin)
    public function getDeletedUsers($conn): array
    {
        if (!$this->isSuperAdmin()) {
            return ["success" => false, "error" => "Superadmin only", "code" => 403];
        }

        try {
            $sql = "SELECT id, old_user_id, name, username, email, role, deleted_at FROM deleted_users ORDER BY deleted_at DESC";
            $result = mysqli_query($conn, $sql);
            if (!$result) throw new Exception(mysqli_error($conn));

            $users = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
            return ["success" => true, "data" => $users];
        } catch (Exception $e) {
            return ["success" => false, "error" => "Database error: " . $e->getMessage(), "code" => 500];
        }
    }

    // Khôi phục tài khoản đã xóa (Chỉ Superadmin)
    public function restoreUser($conn, int $deleted_record_id): array
    {
        if (!$this->isSuperAdmin()) {
            return ["success" => false, "error" => "Superadmin only", "code" => 403];
        }

        try {
            // 1. Lấy thông tin tài khoản đã xóa
            $sql = "SELECT * FROM deleted_users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $deleted_record_id);
            mysqli_stmt_execute($stmt);
            $deleted_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$deleted_user) {
                return ["success" => false, "error" => "Không tìm thấy tài khoản đã xóa", "code" => 404];
            }

            // 2. Kiểm tra xem username hoặc email đã bị trùng với tài khoản đang hoạt động khác chưa
            $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "ss", $deleted_user['username'], $deleted_user['email']);
            mysqli_stmt_execute($check_stmt);
            $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($check_stmt));

            if ($duplicate) {
                return [
                    "success" => false, 
                    "error" => "Không thể khôi phục: Username hoặc Email của tài khoản này đã được sử dụng bởi một tài khoản đang hoạt động khác!",
                    "code" => 400
                ];
            }

            // 3. Nếu password là NULL (do các tài khoản đã bị xóa trước đó không có cột password), đặt password mặc định là BCRYPT hash của '123456'
            $password = $deleted_user['password'] ?: password_hash("123456", PASSWORD_BCRYPT);
            $avatar = $deleted_user['avatar'];
            $role = $deleted_user['role'] ?: 'user';

            // 4. Khôi phục lại tài khoản vào bảng users
            $insert_sql = "INSERT INTO users (name, username, email, password, phone, role, avatar, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param(
                $insert_stmt, 
                "sssssss", 
                $deleted_user['name'], 
                $deleted_user['username'], 
                $deleted_user['email'], 
                $password, 
                $deleted_user['phone'], 
                $role, 
                $avatar
            );

            if (!mysqli_stmt_execute($insert_stmt)) {
                throw new Exception("Lỗi khi khôi phục tài khoản hoạt động: " . mysqli_error($conn));
            }

            // 5. Xóa khỏi danh sách tài khoản đã xóa
            $delete_sql = "DELETE FROM deleted_users WHERE id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "i", $deleted_record_id);
            mysqli_stmt_execute($delete_stmt);

            return [
                "success" => true,
                "message" => "Khôi phục tài khoản thành công!"
            ];

        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => "Server error: " . $e->getMessage(),
                "code" => 500
            ];
        }
    }
}
