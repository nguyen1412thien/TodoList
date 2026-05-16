let currentTargetUserId = null;
let currentTargetUserRole = null;
let allUsersList = [];

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
