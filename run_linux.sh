#!/bin/bash

# Kiểm tra Docker đã chạy chưa
if ! docker info > /dev/null 2>&1; then
    echo "Lỗi: Docker chưa được bật. Vui lòng bật Docker Desktop trước."
    exit 1
fi

echo "--- Đang khởi động ZenTask (Docker) ---"

# Khởi động các container
docker-compose up -d

echo "Đang đợi cơ sở dữ liệu sẵn sàng..."
sleep 10

# Mở trình duyệt
echo "Đã xong! Mở ứng dụng tại: http://localhost:8080"
open http://localhost:8080
