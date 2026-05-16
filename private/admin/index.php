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
            <div class="bg-primary text-white text-[10px] px-3 py-1 rounded-full uppercase tracking-widest font-black card-shadow">Admin Dashboard</div>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-3xl card-shadow overflow-hidden border border-primary/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary-light/50 border-b border-primary/10">
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-muted">Người dùng</th>
                            <th class="px-6 py-4 text-xs font-bold text-muted uppercase tracking-widest text-center">Vai trò</th>
                            <th class="px-6 py-4 text-xs font-bold text-muted uppercase tracking-widest text-left">Ngày tham gia</th>
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
        <div class="mt-8 flex justify-between items-center text-muted text-xs font-bold uppercase tracking-widest px-4">
            <div id="total-users-count">Tổng cộng: 0 tài khoản</div>
            <div>Hệ thống ZenTask v2.0</div>
        </div>

        <!-- Role Change Modal -->
        <div id="role-modal" class="modal-overlay hidden" style="z-index: 100;">
            <div class="modal-content !max-w-sm text-center">
                <div class="bg-primary/10 mx-auto mb-4 flex items-center justify-center text-primary" style="width: 4rem; height: 4rem; border-radius: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                </div>
                <h2 class="font-bold text-dark text-xl mb-2">Thay đổi vai trò</h2>
                <p id="role-modal-desc" class="text-muted text-sm mb-6">Chọn vai trò mới cho người dùng này.</p>
                
                <div class="flex flex-col gap-3 mb-6">
                    <button id="btn-set-user" onclick="confirmRoleChange('user')" class="flex items-center justify-between p-4 rounded-2xl border-2 border-transparent bg-gray-50 hover:bg-gray-100 transition-all text-left group">
                        <div>
                            <div class="font-bold text-dark group-hover:text-primary transition-colors">Người dùng (User)</div>
                            <div class="text-[10px] text-muted">Quyền hạn cơ bản, chỉ quản lý task cá nhân.</div>
                        </div>
                        <div class="radio-circle"></div>
                    </button>
                    <button id="btn-set-admin" onclick="confirmRoleChange('admin')" class="flex items-center justify-between p-4 rounded-2xl border-2 border-transparent bg-gray-50 hover:bg-gray-100 transition-all text-left group">
                        <div>
                            <div class="font-bold text-dark group-hover:text-primary transition-colors">Quản trị viên (Admin)</div>
                            <div class="text-[10px] text-muted">Toàn quyền hệ thống và quản lý tài khoản.</div>
                        </div>
                        <div class="radio-circle"></div>
                    </button>
                </div>

                <button onclick="closeRoleModal()" class="btn btn-block btn-white py-3 rounded-xl font-bold text-muted uppercase tracking-widest text-[10px]">Hủy bỏ</button>
            </div>
        </div>
    </div>

    <script src="../../public/js/dialog.js?v=<?php echo filemtime('../../public/js/dialog.js'); ?>"></script>
    <script>
        let currentTargetUserId = null;
        let currentTargetUserRole = null;

        // Bảo mật: Nếu không phải admin thì quay về trang chủ
        if (!isAdmin()) {
            window.location.href = '../../public/main/';
        }

        async function loadUsers() {
            try {
                const response = await apiCall('/admin/users.php');
                if (response.status === 200 && response.data.success) {
                    renderUsers(response.data.data);
                } else {
                    const errorMsg = response.data ? (response.data.error || response.data.message) : 'Lỗi kết nối';
                    await showDialog('Lỗi', errorMsg, 'error');
                    document.getElementById('user-list-body').innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-danger font-bold">Lỗi: ${errorMsg}</td></tr>`;
                }
            } catch (error) {
                console.error(error);
                await showDialog('Lỗi hệ thống', error.message, 'error');
            }
        }

        function renderUsers(users) {
            const body = document.getElementById('user-list-body');
            const count = document.getElementById('total-users-count');
            const myId = JSON.parse(atob(getToken().split('.')[1])).user_id;
            
            count.innerText = `Tổng cộng: ${users.length} tài khoản`;
            
            if (users.length === 0) {
                body.innerHTML = '<tr><td colspan="4" class="px-6 py-10 text-center text-muted">Không có người dùng nào</td></tr>';
                return;
            }

            body.innerHTML = users.map(user => {
                // Kiểm tra xem có được phép sửa role của người này không
                const canEdit = (user.role === 'user' || user.id == myId);
                
                return `
                <tr class="border-b border-gray-50 hover:bg-primary-light/20 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center text-primary font-bold overflow-hidden border border-primary/10">
                                ${user.avatar ? 
                                    `<img src="${user.avatar.startsWith('http') ? user.avatar : `${PROJECT_ROOT}/../${user.avatar}`}" class="w-full h-full object-cover">` 
                                    : user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-bold text-dark text-sm">${user.name}</div>
                                <div class="text-muted text-xs">@${user.username}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button 
                            onclick="${canEdit ? `openRoleModal(${user.id}, '${user.role}', '${user.name}')` : `alert('Bạn không có quyền sửa vai trò của Admin khác!')`}"
                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all ${user.role === 'admin' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400'} ${canEdit ? 'hover:scale-110 active:scale-95 cursor-pointer shadow-sm' : 'opacity-80 cursor-not-allowed'}"
                        >
                            ${user.role}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-muted">
                        ${new Date(user.created_at).toLocaleDateString('vi-VN')}
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <!-- Toggle Status Button -->
                        <button onclick="${canEdit ? `toggleUserStatus(${user.id})` : `showDialog('Lỗi quyền hạn', 'Quyền hạn Admin khác: Không thể thao tác', 'error')`}"
                                class="p-2 transition-all rounded-xl hover:bg-gray-100 ${canEdit ? (user.status === 'locked' ? 'text-warning' : 'text-success') : 'text-danger cursor-not-allowed'}"
                                title="${canEdit ? (user.status === 'locked' ? 'Mở khóa tài khoản' : 'Khóa tài khoản') : 'Không thể thao tác'}">
                            ${user.status === 'locked' ? 
                                `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>` : 
                                (canEdit ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>` : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>`)
                            }
                        </button>
                        
                        <!-- Delete User Button -->
                        ${canEdit && user.id != myId ? `
                        <button onclick="deleteUser(${user.id}, '${user.name}')" 
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
            `}).join('');
        }

        function openRoleModal(userId, role, name) {
            currentTargetUserId = userId;
            currentTargetUserRole = role;
            document.getElementById('role-modal-desc').innerText = `Thay đổi vai trò cho ${name}`;
            document.getElementById('role-modal').classList.remove('hidden');
            
            // Highlight vai trò hiện tại
            document.getElementById('btn-set-user').classList.toggle('border-primary', role === 'user');
            document.getElementById('btn-set-admin').classList.toggle('border-primary', role === 'admin');
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
                        logout(); // Gọi hàm logout để xóa Token cũ
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

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>
</html>
