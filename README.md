# 🚀 ZenTask - TodoList Management System

ZenTask là ứng dụng quản lý công việc cá nhân hiện đại, mượt mà và dễ dàng triển khai. Dự án đã được đóng gói hoàn chỉnh, cho phép bạn khởi động chỉ với **một cú nhấp chuột**.

---

## 🛠 Cách Khởi Động Nhanh (Khuyên dùng)

Yêu cầu duy nhất: Máy tính của bạn đã cài đặt **Docker Desktop**.

### 🍎 Đối với người dùng macOS
1. Nhấp đúp vào file `run_macos.command`.
2. Ứng dụng sẽ tự động khởi động và mở trình duyệt tại địa chỉ: `http://localhost:8088`.

### 🪟 Đối với người dùng Windows
1. Nhấp đúp vào file `run_windows.bat`.
2. Ứng dụng sẽ tự động khởi động và mở trình duyệt tại địa chỉ: `http://localhost:8088`.

### 🐧 Đối với người dùng Linux
1. Mở Terminal và chạy lệnh: `./run_linux.sh`.

---

## ⚙️ Thông tin kỹ thuật (Docker)

- **Cổng ứng dụng:** `8088`
- **Cổng Database (MySQL):** `3307`
- **Tài khoản mặc định:** 
  - Username: `admin`
  - Password: `123456`

---

## 💻 Triển khai thủ công (XAMPP/MAMP)

Nếu bạn không muốn dùng Docker, bạn có thể chạy theo cách truyền thống:
1. Copy toàn bộ thư mục dự án vào thư mục `htdocs` của XAMPP.
2. Đổi tên thư mục dự án thành `TodoList`.
3. Import file cơ sở dữ liệu từ thư mục `database/schema.sql` và `database/seed.sql` vào phpMyAdmin.
4. Truy cập: `http://localhost/TodoList/`.

---

## 📁 Cấu trúc dự án
- `/api`: Các điểm cuối API xử lý logic.
- `/app`: Chứa Models và Controllers (MVC).
- `/config`: Cấu hình kết nối Database.
- `/public`: Giao diện người dùng (HTML, CSS, JS).
- `/database`: Chứa các file SQL mẫu.

---

**Chúc bạn quản lý công việc hiệu quả với ZenTask!** 😊
