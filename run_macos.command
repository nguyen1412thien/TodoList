#!/bin/bash
# Tự động di chuyển vào thư mục chứa file này
cd "$(dirname "$0")"

# Kiểm tra Docker đã chạy chưa
if ! docker info > /dev/null 2>&1; then
    echo "===================================================="
    echo "LỖI: Docker Desktop chưa được bật!"
    echo "Vui lòng mở Docker Desktop từ Applications và thử lại."
    echo "===================================================="
    read -n 1 -s -r -p "Nhấn phím bất kỳ để thoát..."
    exit 1
fi

echo "----------------------------------------------------"
echo "   ĐANG KHỞI ĐỘNG ZENTASK (DOCKER MODE)             "
echo "----------------------------------------------------"

# Khởi động các container và build lại image nếu có thay đổi
docker-compose up -d --build

echo "Đang khởi tạo hệ thống (vui lòng đợi 15 giây)..."
sleep 15

# Mở trình duyệt
echo "Đã xong! Mở ứng dụng tại: http://localhost:8088"
open http://localhost:8088

echo "----------------------------------------------------"
echo "Ứng dụng đang chạy ngầm. Bạn có thể đóng cửa sổ này."
echo "Để tắt ứng dụng, hãy chạy lệnh: docker-compose down"
echo "----------------------------------------------------"
