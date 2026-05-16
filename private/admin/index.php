<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenTask - Quản trị hệ thống</title>
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
    <script>
        requireAuth();
        if (!isAdmin()) {
            window.location.href = '../../public/main/';
        }
    </script>
</head>
<body class="bg-primary-light/30">
    <div id="app" class="container max-w-4xl mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <button class="btn-icon btn-white card-shadow text-muted" onclick="window.location.href='../../public/user/'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>
                <div>
                    <h1 class="font-black text-dark text-2xl tracking-tight">Quản trị hệ thống</h1>
                    <p class="text-muted text-sm font-medium">Quản lý danh sách tài khoản người dùng</p>
                </div>
            </div>
            <div id="admin-badge" class="bg-primary text-white text-[10px] px-3 py-1 rounded-full uppercase tracking-widest font-black card-shadow">Admin Dashboard</div>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-3xl card-shadow overflow-hidden border border-primary/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary-light/95 border-b border-primary-light/40">
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted">Người dùng</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted text-center">Vai trò</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted text-center">Ngày tham gia</th>
                            <th class="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody id="user-list-body">
                        <!-- JS will populate this -->
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-2 text-muted">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                    <span class="text-sm font-medium">Đang tải danh sách...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Stats -->
        <div class="mt-6 px-4">
            <div id="total-users-count" class="inline-flex items-center gap-2 bg-primary-light text-primary px-4 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider shadow-sm border border-primary/10 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="opacity-95"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>Tổng cộng: <strong class="text-sm font-black text-primary" id="total-users-number">0</strong> tài khoản</span>
            </div>
        </div>

        <!-- Deleted Users Section (Superadmin only) -->
        <div id="deleted-users-section" class="hidden mt-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-red-100 p-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-dark text-lg tracking-tight">Tài khoản đã xóa</h2>
                    <p class="text-muted text-xs font-medium">Lưu trữ các tài khoản đã bị xóa khỏi hệ thống</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl card-shadow overflow-hidden border border-red-100 mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-red-50/90 border-b border-red-100/60">
                                <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted">Người dùng</th>
                                <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted text-center">Vai trò cũ</th>
                                <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted text-center">Ngày xóa</th>
                                <th class="px-6 py-4 text-right text-[10px] uppercase tracking-widest font-black text-muted">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="deleted-users-body">
                            <tr><td colspan="4" class="px-6 py-10 text-center text-muted text-sm">Đang tải...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex justify-end items-center mt-12 pb-4" style="border-top: 1px solid var(--border-light); pt-6">
            <span class="text-[10px] font-bold tracking-widest uppercase" style="color: #cbd5e1; letter-spacing: 0.05em; margin-top: 1rem;">PROJECT OF 14 DEC</span>
        </footer>

        <!-- Role Change Modal -->
        <div id="role-modal" class="modal-overlay hidden" style="z-index: 100;">
            <div class="modal-content !max-w-sm text-center">
                <div class="bg-primary/10 mx-auto mb-4 flex items-center justify-center text-primary" style="width: 4rem; height: 4rem; border-radius: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-primary"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h2 class="font-bold text-dark text-xl mb-2">Thay đổi vai trò</h2>
                <p id="role-modal-desc" class="text-muted text-sm mb-6">Chọn vai trò mới cho người dùng này.</p>
                
                <div class="flex flex-col gap-3 mb-6">
                    <button id="btn-set-user" onclick="confirmRoleChange('user')" class="flex items-center justify-between p-4 rounded-2xl border-2 border-transparent bg-gray-50 hover:bg-gray-100 transition-all text-left group">
                        <div>
                            <div class="font-bold text-dark group-hover:text-primary transition-colors">Người dùng</div>
                            <div class="text-[10px] text-muted">Quyền hạn cơ bản, chỉ quản lý task cá nhân.</div>
                        </div>
                        <div class="radio-circle"></div>
                    </button>
                    <button id="btn-set-admin" onclick="confirmRoleChange('admin')" class="flex items-center justify-between p-4 rounded-2xl border-2 border-transparent bg-gray-50 hover:bg-gray-100 transition-all text-left group">
                        <div>
                            <div class="font-bold text-dark group-hover:text-primary transition-colors">Quản trị viên</div>
                            <div class="text-[10px] text-muted">Quản lý tài khoản user thông thường.</div>
                        </div>
                        <div class="radio-circle"></div>
                    </button>
                    <button id="btn-set-superadmin" class="hidden flex items-center justify-between p-4 rounded-2xl border-2 border-transparent bg-amber-50 hover:bg-amber-100 transition-all text-left group" onclick="confirmRoleChange('superadmin')">
                        <div>
                            <div class="font-bold text-amber-700 group-hover:text-amber-900 transition-colors flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                Quản trị viên tối cao
                            </div>
                            <div class="text-[10px] text-amber-600">Toàn quyền tuyệt đối, quản lý toàn bộ hệ thống.</div>
                        </div>
                        <div class="radio-circle"></div>
                    </button>
                </div>

                <button onclick="closeRoleModal()" class="btn btn-block btn-white py-3 rounded-xl font-bold text-muted uppercase tracking-widest text-[10px]">Hủy bỏ</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal (Superadmin only) -->
    <div id="edit-user-modal" class="modal-overlay hidden" style="z-index: 100;">
        <div class="modal-content !max-w-md">
            <div class="text-center mb-6">
                <div class="bg-primary/10 mx-auto mb-3 flex items-center justify-center text-primary" style="width: 3.5rem; height: 3.5rem; border-radius: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <h2 class="font-bold text-dark text-xl">Chỉnh sửa tài khoản</h2>
                <p class="text-muted text-xs">Cập nhật toàn bộ thông tin của thành viên</p>
            </div>

            <div class="space-y-4 mb-6 text-left">
                <input type="hidden" id="edit-user-id">
                
                <div>
                    <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-1.5">Họ và tên</label>
                    <input type="text" id="edit-user-name" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm font-semibold text-dark">
                </div>

                <div>
                    <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-1.5">Tên đăng nhập</label>
                    <input type="text" id="edit-user-username" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm font-semibold text-dark">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" id="edit-user-email" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm font-semibold text-dark">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-1.5">Số điện thoại</label>
                        <input type="text" id="edit-user-phone" placeholder="Chưa cập nhật" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm font-semibold text-dark">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-1.5">Mật khẩu mới (Bỏ trống nếu không đổi)</label>
                    <input type="password" id="edit-user-password" placeholder="••••••••" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm font-semibold text-dark">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Vai trò (Role Selector) -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Vai trò</label>
                        <input type="hidden" id="edit-user-role" value="user">
                        <div class="flex flex-col gap-2">
                            <button type="button" id="edit-role-btn-user" onclick="selectEditRole('user')" class="flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group">
                                <div>
                                    <div class="font-bold text-dark text-xs group-hover:text-primary transition-colors">Người dùng</div>
                                    <div class="text-[9px] text-muted">Quyền hạn cơ bản.</div>
                                </div>
                                <div class="radio-circle"></div>
                            </button>
                            <button type="button" id="edit-role-btn-admin" onclick="selectEditRole('admin')" class="flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group">
                                <div>
                                    <div class="font-bold text-dark text-xs group-hover:text-primary transition-colors">Quản trị viên</div>
                                    <div class="text-[9px] text-muted">Quản lý user thường.</div>
                                </div>
                                <div class="radio-circle"></div>
                            </button>
                            <button type="button" id="edit-role-btn-superadmin" onclick="selectEditRole('superadmin')" class="flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group">
                                <div>
                                    <div class="font-bold text-amber-700 text-xs group-hover:text-amber-900 transition-colors flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        Quản trị tối cao
                                    </div>
                                    <div class="text-[9px] text-amber-600">Toàn quyền hệ thống.</div>
                                </div>
                                <div class="radio-circle"></div>
                            </button>
                        </div>
                    </div>

                    <!-- Trạng thái (Status Selector) -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Trạng thái</label>
                        <input type="hidden" id="edit-user-status" value="active">
                        <div class="flex flex-col gap-2">
                            <button type="button" id="edit-status-btn-active" onclick="selectEditStatus('active')" class="flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group">
                                <div>
                                    <div class="font-bold text-emerald-600 text-xs group-hover:text-emerald-700 transition-colors">Hoạt động</div>
                                    <div class="text-[9px] text-muted font-medium">Tài khoản mở khóa.</div>
                                </div>
                                <div class="radio-circle"></div>
                            </button>
                            <button type="button" id="edit-status-btn-locked" onclick="selectEditStatus('locked')" class="flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group">
                                <div>
                                    <div class="font-bold text-rose-600 text-xs group-hover:text-rose-700 transition-colors">Bị khóa</div>
                                    <div class="text-[9px] text-muted font-medium">Tài khoản tạm ngưng.</div>
                                </div>
                                <div class="radio-circle"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="closeEditUserModal()" class="btn flex-1 btn-white py-3 rounded-2xl font-bold text-muted uppercase tracking-widest text-[10px]">Hủy bỏ</button>
                <button onclick="saveUserFullDetails()" class="btn flex-1 btn-primary py-3 rounded-2xl font-bold uppercase tracking-widest text-[10px]">Lưu thay đổi</button>
            </div>
        </div>
    </div>

    <script src="../../public/js/dialog.js?v=<?php echo filemtime('../../public/js/dialog.js'); ?>"></script>
    <script src="../../public/js/admin.js?v=<?php echo filemtime('../../public/js/admin.js'); ?>"></script>
</body>
</html>
