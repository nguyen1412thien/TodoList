<?php
// Lấy thông tin người dùng hiện tại (nếu cần hiển thị email cũ)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenTask - Bảo mật & Tài khoản</title>
    <link rel="stylesheet" href="../../public/style.css?v=<?php echo filemtime('../../public/style.css'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', 'primary-hover': '#1d4ed8', 'primary-light': '#eff6ff',
                        main: '#111827', dark: '#374151', muted: '#9ca3af',
                        danger: '#f43f5e', 'danger-light': '#ffe4e6',
                        success: '#10b981', 'success-light': '#ecfdf5',
                        warning: '#f97316', 'warning-light': '#fff7ed'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div id="app" class="container max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <button class="btn-icon btn-white card-shadow text-muted" onclick="window.location.href='../../public/user/'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </button>
            <h1 class="font-bold text-dark text-xl">Bảo mật tài khoản</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar Tabs -->
            <div class="md:col-span-1 space-y-2">
                <button onclick="switchTab('password')" id="tab-password" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all">Mật khẩu</button>
                <button onclick="switchTab('contact')" id="tab-contact" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all">Liên lạc</button>
                <button onclick="switchTab('history')" id="tab-history" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all">Lịch sử đăng nhập</button>
                <button onclick="switchTab('danger')" id="tab-danger" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all text-danger">Vùng nguy hiểm</button>
            </div>

            <!-- Content Area -->
            <div class="md:col-span-3 bg-white rounded-3xl p-8 card-shadow min-h-[400px]">
                <!-- Tab: Password -->
                <div id="content-password" class="tab-content">
                    <h2 class="text-xl font-bold text-dark mb-6">Đổi mật khẩu</h2>
                    <div class="space-y-4 max-w-md">
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase mb-2">Mật khẩu hiện tại</label>
                            <input type="password" id="old-password" class="w-full p-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-primary outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase mb-2">Mật khẩu mới</label>
                            <input type="password" id="new-password" class="w-full p-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-primary outline-none transition-all">
                        </div>
                        <button onclick="updatePassword()" class="btn btn-primary w-full py-4 rounded-2xl font-bold">Cập nhật mật khẩu</button>
                    </div>
                </div>

                <!-- Tab: Contact -->
                <div id="content-contact" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-dark mb-6">Thông tin liên lạc</h2>
                    <div class="space-y-6 max-w-md">
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase mb-2">Địa chỉ Email</label>
                            <div class="flex gap-2">
                                <input type="email" id="email-input" class="flex-1 p-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-primary outline-none transition-all">
                                <button onclick="updateInfo('email')" class="btn btn-white px-6 rounded-2xl font-bold">Lưu</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase mb-2">Số điện thoại</label>
                            <div class="flex gap-2">
                                <input type="tel" id="phone-input" class="flex-1 p-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-primary outline-none transition-all" placeholder="Chưa có số điện thoại">
                                <button onclick="updateInfo('phone')" class="btn btn-white px-6 rounded-2xl font-bold">Lưu</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: History -->
                <div id="content-history" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-dark mb-6">Lịch sử đăng nhập</h2>
                    <div id="history-list" class="space-y-4">
                        <!-- Loading or content -->
                    </div>
                </div>

                <!-- Tab: Danger -->
                <div id="content-danger" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-danger mb-2">Vùng nguy hiểm</h2>
                    <p class="text-muted text-sm mb-8">Khi khóa tài khoản, bạn sẽ không thể đăng nhập hoặc thực hiện bất kỳ thao tác nào cho đến khi Admin mở lại.</p>
                    <button onclick="lockAccount()" class="bg-danger text-white px-8 py-4 rounded-2xl font-bold hover:bg-red-600 transition-all card-shadow shadow-danger/20">KHÓA TÀI KHOẢN TẠM THỜI</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/api.js?v=<?php echo filemtime('../../public/js/api.js'); ?>"></script>
    <script src="../../public/js/security.js?v=<?php echo filemtime('../../public/js/security.js'); ?>"></script>
</body>
</html>
