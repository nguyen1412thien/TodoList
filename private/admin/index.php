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
    <script>
        let currentTargetUserId = null;
        let currentTargetUserRole = null;
        let allUsersList = [];

        if (!isAdmin()) {
            window.location.href = '../../public/main/';
        }

        async function loadUsers() {
            try {
                const response = await apiCall('/admin/users.php');
                if (response.status === 200 && response.data.success) {
                    allUsersList = response.data.data;
                    renderUsers(allUsersList);
                } else {
                    const errorMsg = response.data ? (response.data.error || response.data.message) : 'Lỗi kết nối';
                    await showDialog('Lỗi', errorMsg, 'error');
                    document.getElementById('user-list-body').innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-danger font-bold">Lỗi: ${errorMsg}</td></tr>`;
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi hệ thống', error.message, 'error');
            }

            if (isSuperAdmin()) {
                const badge = document.getElementById('admin-badge');
                if (badge) {
                    badge.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="inline-block mr-1 text-white align-middle" style="margin-top: -2px;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        Superadmin Dashboard
                    `;
                    badge.className = 'bg-amber-500 text-white text-[10px] px-3 py-1 rounded-full uppercase tracking-widest font-black card-shadow flex items-center gap-1';
                }
                const delSec = document.getElementById('deleted-users-section');
                if (delSec) {
                    delSec.classList.remove('hidden');
                }
                loadDeletedUsers();
            }
        }

        async function loadDeletedUsers() {
            try {
                const response = await apiCall('/admin/deleted_users.php');
                const tbody = document.getElementById('deleted-users-body');
                if (!tbody) return;

                if (response.status === 200 && response.data.success) {
                    const dUsers = response.data.data;
                    if (dUsers.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-10 text-center text-muted text-sm font-medium">Chưa có tài khoản nào bị xóa</td></tr>';
                        return;
                    }
                    tbody.innerHTML = dUsers.map(u => `
                        <tr class="border-b border-red-50 hover:bg-red-50/20 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-dark text-sm">${u.name}</div>
                                <div class="text-muted text-xs">@${u.username} (${u.email})</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-gray-100 text-gray-400">${u.role}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-muted text-center">
                                ${new Date(u.deleted_at).toLocaleDateString('vi-VN')} ${new Date(u.deleted_at).toLocaleTimeString('vi-VN')}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="restoreUser(${u.id}, '${u.name}')" 
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500 hover:bg-emerald-600 active:scale-95 transition-all text-white shadow-sm flex items-center gap-1 ml-auto"
                                        title="Khôi phục tài khoản này">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                                    Khôi phục
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-danger font-bold">Lỗi tải dữ liệu</td></tr>`;
                }
            } catch (error) {
                console.error(error);
            }
        }

        function renderUsers(users) {
            const body = document.getElementById('user-list-body');
            const count = document.getElementById('total-users-count');
            const myId = JSON.parse(atob(getToken().split('.')[1])).user_id;
            const isSuper = isSuperAdmin();
            
            const numberSpan = document.getElementById('total-users-number');
            if (numberSpan) {
                numberSpan.innerText = users.length;
            } else {
                count.innerText = `Tổng cộng: ${users.length} tài khoản`;
            }
            
            if (users.length === 0) {
                body.innerHTML = '<tr><td colspan="4" class="px-6 py-10 text-center text-muted">Không có người dùng nào</td></tr>';
                return;
            }

            body.innerHTML = users.map(user => {
                const canEdit = isSuper ? (user.role !== 'superadmin' || user.id == myId) : (user.role === 'user' || user.id == myId);
                const canChangeRole = isSuper && (user.role !== 'superadmin' || user.id == myId);
                
                return `
                <tr class="border-b border-gray-50 hover:bg-primary-light/20 transition-all ${isSuper ? 'cursor-pointer group/row' : ''}" ${isSuper ? `onclick="openEditUserModal(${user.id})"` : ''} title="${isSuper ? 'Click bất kỳ vị trí nào để sửa toàn bộ thông tin tài khoản' : ''}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center text-primary font-bold overflow-hidden border border-primary/10 transition-transform ${isSuper ? 'group-hover/row:scale-105' : ''}">
                                ${user.avatar ? 
                                    `<img src="${user.avatar.startsWith('http') ? user.avatar : `${PROJECT_ROOT}/../${user.avatar}`}" class="w-full h-full object-cover">` 
                                    : user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-bold text-dark text-sm ${isSuper ? 'group-hover/row:text-primary transition-colors' : ''}">${user.name}</div>
                                <div class="text-muted text-xs">@${user.username}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button 
                            onclick="event.stopPropagation(); ${canChangeRole ? `openRoleModal(${user.id}, '${user.role}', '${user.name}')` : `showDialog('Lỗi quyền hạn', 'Chỉ Quản trị viên tối cao mới có quyền thay đổi vai trò!', 'error')`}"
                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all ${user.role === 'superadmin' ? 'bg-amber-500 text-white' : (user.role === 'admin' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400')} ${canChangeRole ? 'hover:scale-110 active:scale-95 cursor-pointer shadow-sm' : 'opacity-80 cursor-not-allowed'}"
                        >
                            ${user.role === 'superadmin' ? 'Quản trị tối cao' : (user.role === 'admin' ? 'Quản trị viên' : 'Người dùng')}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-muted text-center">
                        ${new Date(user.created_at).toLocaleDateString('vi-VN')}
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <button onclick="event.stopPropagation(); ${canEdit ? `toggleUserStatus(${user.id})` : `showDialog('Lỗi quyền hạn', 'Quyền hạn Admin khác: Không thể thao tác', 'error')`}"
                                class="p-2 transition-all rounded-xl hover:bg-gray-100 ${canEdit ? (user.status === 'locked' ? 'text-warning' : 'text-success') : 'text-danger cursor-not-allowed'}"
                                title="${canEdit ? (user.status === 'locked' ? 'Mở khóa tài khoản' : 'Khóa tài khoản') : 'Không thể thao tác'}">
                            ${user.status === 'locked' ? 
                                `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>` : 
                                (canEdit ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>` : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>`)
                            }
                        </button>
                        ${canEdit && user.id != myId ? `
                        <button onclick="event.stopPropagation(); deleteUser(${user.id}, '${user.name}')" 
                                class="p-2 transition-all rounded-xl hover:bg-danger-light text-danger"
                                title="Xóa tài khoản này">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                        ` : `
                        <div class="p-2 text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </div>
                        `}
                    </td>
                </tr>
                `;
            }).join('');
        }

        function openRoleModal(userId, role, name) {
            currentTargetUserId = userId;
            currentTargetUserRole = role;
            document.getElementById('role-modal-desc').innerText = `Thay đổi vai trò cho ${name}`;
            document.getElementById('role-modal').classList.remove('hidden');
            
            document.getElementById('btn-set-user').classList.toggle('border-primary', role === 'user');
            document.getElementById('btn-set-admin').classList.toggle('border-primary', role === 'admin');
            
            const btnSuper = document.getElementById('btn-set-superadmin');
            if (isSuperAdmin()) {
                btnSuper.classList.remove('hidden');
                btnSuper.classList.toggle('border-amber-500', role === 'superadmin');
            } else {
                btnSuper.classList.add('hidden');
            }
        }

        function closeRoleModal() {
            document.getElementById('role-modal').classList.add('hidden');
        }

        async function confirmRoleChange(newRole) {
            if (newRole === currentTargetUserRole) {
                closeRoleModal();
                return;
            }

            const myId = JSON.parse(atob(getToken().split('.')[1])).user_id;
            const isSelf = currentTargetUserId == myId;

            if (isSelf && newRole === 'user') {
                closeRoleModal();
                if (!await showConfirm('Cảnh báo', 'Bạn đang tự hạ quyền của chính mình. Bạn sẽ bị thoát khỏi trang Admin ngay lập tức. Tiếp tục?', 'Đồng ý hạ quyền', true)) return;
            }

            try {
                const response = await apiCall('/admin/update_role.php', 'POST', {
                    target_user_id: currentTargetUserId,
                    new_role: newRole
                });

                if (response.status === 200 && response.data.success) {
                    closeRoleModal();
                    if (isSelf && newRole === 'user') {
                        await showDialog('Thành công', 'Bạn đã tự hạ quyền. Hệ thống sẽ đăng xuất để cập nhật lại vai trò.', 'success');
                        logout();
                    } else {
                        loadUsers();
                    }
                } else {
                    await showDialog('Lỗi', response.data.error || 'Không thể cập nhật', 'error');
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi', 'Lỗi kết nối hệ thống', 'error');
            }
        }

        async function toggleUserStatus(userId) {
            if (!await showConfirm('Xác nhận', 'Bạn có muốn thay đổi trạng thái (Khóa/Mở khóa) của tài khoản này không?')) return;
            
            try {
                const response = await apiCall('/admin/update_status.php', 'POST', {
                    target_user_id: userId
                });

                if (response.status === 200 && response.data.success) {
                    loadUsers();
                } else {
                    await showDialog('Lỗi', response.data.error || 'Không thể thao tác', 'error');
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi', 'Lỗi kết nối hệ thống', 'error');
            }
        }

        async function deleteUser(userId, name) {
            if (!await showConfirm('CẢNH BÁO', `Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản của ${name}? Toàn bộ dữ liệu của họ sẽ được chuyển vào kho lưu trữ đã xóa.`, 'Tiếp tục', true)) return;
            if (!await showConfirm('XÁC NHẬN LẦN CUỐI', `Xóa ${name} khỏi hệ thống?`, 'XÓA', true)) return;

            try {
                const response = await apiCall('/admin/delete_user.php', 'POST', {
                    target_user_id: userId
                });

                if (response.status === 200 && response.data.success) {
                    await showDialog('Thành công', 'Tài khoản đã bị xóa thành công!', 'success');
                    loadUsers();
                } else {
                    await showDialog('Lỗi', response.data.error || 'Không thể xóa', 'error');
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi', 'Lỗi kết nối hệ thống', 'error');
            }
        }

        async function restoreUser(deletedRecordId, name) {
            if (!await showConfirm('Xác nhận khôi phục', `Bạn có chắc chắn muốn KHÔI PHỤC tài khoản của ${name} về trạng thái hoạt động bình thường không?`, 'Khôi phục', false)) return;

            try {
                const response = await apiCall('/admin/restore_user.php', 'POST', {
                    deleted_record_id: deletedRecordId
                });

                if (response.status === 200 && response.data.success) {
                    await showDialog('Thành công', `Đã khôi phục tài khoản ${name} thành công!`, 'success');
                    loadUsers();
                } else {
                    await showDialog('Lỗi', response.data.error || 'Không thể khôi phục', 'error');
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi', 'Lỗi kết nối hệ thống', 'error');
            }
        }

        function selectEditRole(role) {
            document.getElementById('edit-user-role').value = role;
            
            const btnUser = document.getElementById('edit-role-btn-user');
            const btnAdmin = document.getElementById('edit-role-btn-admin');
            const btnSuper = document.getElementById('edit-role-btn-superadmin');

            btnUser.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group cursor-pointer";
            btnAdmin.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group cursor-pointer";
            btnSuper.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group cursor-pointer";

            btnUser.querySelector('.radio-circle').className = "radio-circle transition-all";
            btnAdmin.querySelector('.radio-circle').className = "radio-circle transition-all";
            btnSuper.querySelector('.radio-circle').className = "radio-circle transition-all";

            if (role === 'user') {
                btnUser.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-primary bg-primary-light/30 hover:bg-primary-light/40 transition-all text-left group cursor-pointer";
                btnUser.querySelector('.radio-circle').className = "radio-circle active bg-primary border-primary transition-all";
            } else if (role === 'admin') {
                btnAdmin.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-primary bg-primary-light/30 hover:bg-primary-light/40 transition-all text-left group cursor-pointer";
                btnAdmin.querySelector('.radio-circle').className = "radio-circle active bg-primary border-primary transition-all";
            } else if (role === 'superadmin') {
                btnSuper.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-amber-500 bg-amber-50/50 hover:bg-amber-100/50 transition-all text-left group cursor-pointer";
                btnSuper.querySelector('.radio-circle').className = "radio-circle active bg-amber-500 border-amber-500 transition-all";
            }
        }

        function selectEditStatus(status) {
            document.getElementById('edit-user-status').value = status;

            const btnActive = document.getElementById('edit-status-btn-active');
            const btnLocked = document.getElementById('edit-status-btn-locked');

            btnActive.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group cursor-pointer";
            btnLocked.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all text-left group cursor-pointer";

            btnActive.querySelector('.radio-circle').className = "radio-circle transition-all";
            btnLocked.querySelector('.radio-circle').className = "radio-circle transition-all";

            if (status === 'active') {
                btnActive.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-50/30 hover:bg-emerald-50/40 transition-all text-left group cursor-pointer";
                btnActive.querySelector('.radio-circle').className = "radio-circle active bg-emerald-500 border-emerald-500 transition-all";
            } else if (status === 'locked') {
                btnLocked.className = "flex items-center justify-between p-3 rounded-2xl border-2 border-rose-500 bg-rose-50/30 hover:bg-rose-50/40 transition-all text-left group cursor-pointer";
                btnLocked.querySelector('.radio-circle').className = "radio-circle active bg-rose-500 border-rose-500 transition-all";
            }
        }

        function openEditUserModal(userId) {
            if (!isSuperAdmin()) return;

            const user = allUsersList.find(u => u.id == userId);
            if (!user) return;

            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-user-name').value = user.name || '';
            document.getElementById('edit-user-username').value = user.username || '';
            document.getElementById('edit-user-email').value = user.email || '';
            document.getElementById('edit-user-phone').value = user.phone || '';
            document.getElementById('edit-user-password').value = ''; 
            
            selectEditRole(user.role || 'user');
            selectEditStatus(user.status || 'active');

            document.getElementById('edit-user-modal').classList.remove('hidden');
        }

        function closeEditUserModal() {
            document.getElementById('edit-user-modal').classList.add('hidden');
        }

        async function saveUserFullDetails() {
            const id = document.getElementById('edit-user-id').value;
            const name = document.getElementById('edit-user-name').value.trim();
            const username = document.getElementById('edit-user-username').value.trim();
            const email = document.getElementById('edit-user-email').value.trim();
            const phone = document.getElementById('edit-user-phone').value.trim();
            const password = document.getElementById('edit-user-password').value.trim();
            const role = document.getElementById('edit-user-role').value;
            const status = document.getElementById('edit-user-status').value;

            if (!name || !username || !email) {
                await showDialog('Lỗi nhập liệu', 'Vui lòng điền đầy đủ Họ tên, Tên đăng nhập và Email!', 'error');
                return;
            }

            const myId = JSON.parse(atob(getToken().split('.')[1])).user_id;
            const isSelf = id == myId;

            if (isSelf && role !== 'superadmin') {
                if (!await showConfirm('Cảnh báo tự hạ quyền', 'Bạn đang tự thay đổi vai trò của chính mình khỏi vai trò Quản trị tối cao. Bạn sẽ bị đăng xuất ngay lập tức. Tiếp tục?', 'Đồng ý', true)) {
                    return;
                }
            }

            if (isSelf && status === 'locked') {
                if (!await showConfirm('Cảnh báo khóa chính mình', 'Bạn đang tự khóa tài khoản của chính mình. Bạn sẽ bị đăng xuất và không thể đăng nhập lại. Tiếp tục?', 'Đồng ý khóa', true)) {
                    return;
                }
            }

            try {
                const response = await apiCall('/admin/update_user.php', 'POST', {
                    id, name, username, email, phone, password, role, status
                });

                if (response.status === 200 && response.data.success) {
                    closeEditUserModal();
                    await showDialog('Thành công', 'Cập nhật thông tin tài khoản thành công!', 'success');
                    
                    if (isSelf && (role !== 'superadmin' || status === 'locked')) {
                        logout();
                    } else {
                        loadUsers();
                    }
                } else {
                    await showDialog('Lỗi', response.data.error || 'Không thể cập nhật tài khoản', 'error');
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi hệ thống', 'Lỗi kết nối hoặc xử lý yêu cầu', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>
</html>
