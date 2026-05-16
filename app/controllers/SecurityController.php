<?php

require_once __DIR__ . "/../models/User.php";

class SecurityController
{
    private $userModel;
    private $current_user_id;

    public function __construct($conn, $user_id)
    {
        $this->userModel = new User($conn);
        $this->current_user_id = $user_id;
    }

    // Đổi mật khẩu
    public function changePassword($old_pass, $new_pass): array
    {
        $user = $this->userModel->findById($this->current_user_id);
        if (!$user || !password_verify($old_pass, $user['password'])) {
            return ["success" => false, "error" => "Mật khẩu cũ không chính xác"];
        }

        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        if ($this->userModel->updateSecurityInfo($this->current_user_id, 'password', $hashed)) {
            return ["success" => true, "message" => "Đổi mật khẩu thành công"];
        }
        return ["success" => false, "error" => "Không thể cập nhật mật khẩu"];
    }

    // Cập nhật thông tin (Email hoặc Số điện thoại)
    public function updateContactInfo($field, $value): array
    {
        if ($this->userModel->updateSecurityInfo($this->current_user_id, $field, $value)) {
            return ["success" => true, "message" => "Cập nhật thành công"];
        }
        return ["success" => false, "error" => "Thông tin này đã được sử dụng bởi tài khoản khác"];
    }

    // Khóa tài khoản
    public function lockAccount(): array
    {
        if ($this->userModel->updateStatus($this->current_user_id, 'locked')) {
            return ["success" => true, "message" => "Tài khoản đã bị khóa"];
        }
        return ["success" => false, "error" => "Lỗi khi khóa tài khoản"];
    }

    // Xóa tài khoản vĩnh viễn
    public function deleteAccount(): array
    {
        if ($this->userModel->deleteAccount($this->current_user_id)) {
            return ["success" => true, "message" => "Tài khoản đã bị xóa vĩnh viễn"];
        }
        return ["success" => false, "error" => "Lỗi khi xóa tài khoản"];
    }

    // Lấy lịch sử đăng nhập (Dữ liệu thật)
    public function getLoginHistory($conn): array
    {
        $sql = "SELECT * FROM login_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $this->current_user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $logs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Rút gọn User Agent để hiển thị đẹp hơn
            $ua = $row['device'];
            $device = "Thiết bị lạ";
            if (strpos($ua, 'Windows') !== false) $device = "Máy tính Windows";
            else if (strpos($ua, 'Macintosh') !== false) $device = "Máy tính Mac";
            else if (strpos($ua, 'iPhone') !== false) $device = "iPhone";
            else if (strpos($ua, 'Android') !== false) $device = "Điện thoại Android";

            $logs[] = [
                "id" => $row['id'],
                "device" => $device,
                "full_ua" => $ua,
                "ip" => $row['ip'],
                "time" => $row['created_at']
            ];
        }

        return [
            "success" => true,
            "data" => $logs
        ];
    }

    // Lấy thông tin cá nhân
    public function getProfile(): array
    {
        $user = $this->userModel->findById($this->current_user_id);
        if ($user) {
            return [
                "success" => true,
                "data" => [
                    "email" => $user['email'],
                    "phone" => $user['phone']
                ]
            ];
        }
        return ["success" => false, "error" => "Không tìm thấy người dùng"];
    }
}
