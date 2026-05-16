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
    <script src="../../public/js/api.js?v=<?php echo filemtime('../../public/js/api.js'); ?>"></script>
    <script>requireAuth();</script>
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
                <button onclick="switchTab('password')" id="tab-password" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-80"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Mật khẩu
                </button>
                <button onclick="switchTab('contact')" id="tab-contact" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-80"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    Liên lạc
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-80"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Lịch sử đăng nhập
                </button>
                <button onclick="switchTab('danger')" id="tab-danger" class="w-full text-left p-4 rounded-2xl font-bold text-sm transition-all text-danger flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-80"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Vùng nguy hiểm
                </button>
            </div>

            <!-- Content Area -->
            <div class="md:col-span-3 bg-white rounded-3xl p-6 card-shadow w-full max-w-xl self-start">
                <!-- Tab: Password -->
                <div id="content-password" class="tab-content">
                    <h2 class="text-xl font-black text-dark mb-6 tracking-tight">Đổi mật khẩu</h2>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-muted uppercase tracking-wider mb-2">Mật khẩu hiện tại</label>
                            <input type="password" id="old-password" class="form-input !w-full !p-3.5" placeholder="Nhập mật khẩu hiện tại">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-muted uppercase tracking-wider mb-2">Mật khẩu mới</label>
                            <input type="password" id="new-password" class="form-input !w-full !p-3.5" placeholder="Nhập mật khẩu mới">
                        </div>
                        <button onclick="updatePassword()" class="btn btn-primary w-full py-3.5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-md shadow-primary/25 hover:scale-[1.01] active:scale-[0.99] transition-all mt-2">Cập nhật mật khẩu</button>
                    </div>
                </div>

                <!-- Tab: Contact -->
                <div id="content-contact" class="tab-content hidden">
                    <h2 class="text-xl font-black text-dark mb-6 tracking-tight">Thông tin liên lạc</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-muted uppercase tracking-wider mb-2">Địa chỉ Email</label>
                            <div class="flex gap-3">
                                <input type="email" id="email-input" class="form-input flex-1 !p-3.5">
                                <button onclick="updateInfo('email')" class="btn btn-white px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] border border-gray-200 shadow-sm hover:scale-[1.02] active:scale-[0.98] transition-all">Lưu</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-muted uppercase tracking-wider mb-2">Số điện thoại</label>
                            <div class="flex gap-3">
                                <input type="tel" id="phone-input" class="form-input flex-1 !p-3.5" placeholder="Chưa có số điện thoại">
                                <button onclick="updateInfo('phone')" class="btn btn-white px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] border border-gray-200 shadow-sm hover:scale-[1.02] active:scale-[0.98] transition-all">Lưu</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: History -->
                <div id="content-history" class="tab-content hidden">
                    <h2 class="text-xl font-black text-dark mb-6 tracking-tight">Lịch sử đăng nhập</h2>
                    <div id="history-list" class="space-y-4">
                        <!-- Loading or content -->
                    </div>
                </div>

                <!-- Tab: Danger -->
                <div id="content-danger" class="tab-content hidden">
                    <h2 class="text-xl font-black text-danger mb-2 tracking-tight">Vùng nguy hiểm</h2>
                    <p class="text-muted text-xs font-medium mb-8">Các thao tác dưới đây có ảnh hưởng nghiêm trọng đến tài khoản và dữ liệu của bạn. Hãy suy nghĩ thật kỹ trước khi thực hiện.</p>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 bg-red-50/50 rounded-3xl border border-red-100/60 gap-4">
                            <div>
                                <div class="font-black text-red-800 text-sm uppercase tracking-wide">Khóa tài khoản tạm thời</div>
                                <div class="text-xs text-red-600 mt-1 max-w-lg leading-relaxed">Bạn sẽ bị đăng xuất và không thể truy cập lại cho đến khi được Admin mở khóa.</div>
                            </div>
                            <button onclick="lockAccount()" class="bg-white text-danger px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-danger hover:text-white transition-all border border-danger/25 shadow-sm whitespace-nowrap self-start sm:self-auto">KHÓA TẠI ĐÂY</button>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 bg-gray-50/50 rounded-3xl border border-gray-200/60 gap-4">
                            <div>
                                <div class="font-black text-dark text-sm uppercase tracking-wide">Xóa vĩnh viễn tài khoản</div>
                                <div class="text-xs text-muted mt-1 max-w-lg leading-relaxed">Toàn bộ dữ liệu công việc và tài khoản sẽ bị xóa khỏi hệ thống. Không thể khôi phục!</div>
                            </div>
                            <button onclick="deleteAccount()" class="bg-dark text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-sm whitespace-nowrap self-start sm:self-auto">XÓA VĨNH VIỄN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex justify-end items-center mt-12 pb-4">
            <span class="text-[10px] font-bold tracking-widest uppercase" style="color: #cbd5e1; letter-spacing: 0.05em;">PROJECT OF 14 DEC</span>
        </footer>
    </div>

    <script src="../../public/js/dialog.js?v=<?php echo filemtime('../../public/js/dialog.js'); ?>"></script>
    <script src="../../public/js/security.js?v=<?php echo filemtime('../../public/js/security.js'); ?>"></script>
</body>
</html>
