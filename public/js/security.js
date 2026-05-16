// Kiểm tra đăng nhập ngay khi load script
requireAuth();

// Tải thông tin người dùng hiện tại khi mở trang
async function loadUserData() {
    try {
        const res = await apiCall('/security/actions.php?action=get_profile');
        if (res.status === 200 && res.data.success) {
            document.getElementById('email-input').value = res.data.data.email || '';
            document.getElementById('phone-input').value = res.data.data.phone || '';
        }
    } catch (error) {
        console.error("Lỗi khi tải thông tin cá nhân:", error);
    }
}

function switchTab(tab) {
    // Ẩn tất cả nội dung
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    
    // Reset style tất cả các nút tab về trạng thái mặc định (inactive)
    document.querySelectorAll('[id^="tab-"]').forEach(b => {
        b.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
        if (b.id === 'tab-danger') {
            b.classList.add('bg-white', 'hover:bg-danger-light');
        } else {
            b.classList.add('bg-white', 'text-muted', 'hover:bg-gray-100');
        }
    });

    // Hiển thị nội dung được chọn
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Đổi màu nút tab đang active
    const btn = document.getElementById('tab-' + tab);
    btn.classList.remove('bg-white', 'text-muted', 'hover:bg-gray-100', 'hover:bg-danger-light');
    btn.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');

    if (tab === 'history') loadHistory();
}

async function updatePassword() {
    const old_password = document.getElementById('old-password').value;
    const new_password = document.getElementById('new-password').value;
    
    if (!old_password || !new_password) {
        await showDialog('Lỗi', 'Vui lòng nhập đủ thông tin', 'error');
        return;
    }

    const res = await apiCall('/security/actions.php?action=change_password', 'POST', { old_password, new_password });
    if (res.status === 200 && res.data.success) {
        await showDialog('Thành công', res.data.message, 'success');
        document.getElementById('old-password').value = '';
        document.getElementById('new-password').value = '';
    } else {
        await showDialog('Lỗi', res.data.error || 'Cập nhật thất bại', 'error');
    }
}

async function updateInfo(type) {
    const val = document.getElementById(type + '-input').value;
    const endpoint = type === 'email' ? 'update_email' : 'update_phone';
    const body = type === 'email' ? { email: val } : { phone: val };

    const res = await apiCall('/security/actions.php?action=' + endpoint, 'POST', body);
    if (res.status === 200 && res.data.success) {
        await showDialog('Thành công', res.data.message, 'success');
        loadUserData(); // Tải lại dữ liệu mới
    } else {
        await showDialog('Lỗi', res.data.error || 'Cập nhật thất bại', 'error');
    }
}

async function lockAccount() {
    if (!await showConfirm('Cảnh báo', 'Bạn có chắc chắn muốn KHÓA tài khoản của mình? Bạn sẽ bị đăng xuất ngay lập tức!', 'Khóa tài khoản', true)) return;

    const res = await apiCall('/security/actions.php?action=lock_account', 'POST');
    if (res.status === 200 && res.data.success) {
        await showDialog('Thành công', 'Tài khoản đã khóa. Tạm biệt!', 'success');
        logout();
    } else {
        await showDialog('Lỗi', res.data.error || 'Không thể khóa', 'error');
    }
}

async function deleteAccount() {
    if (!await showConfirm('CẢNH BÁO NGUY HIỂM', 'Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản của mình? Toàn bộ dữ liệu công việc sẽ bị xóa theo và KHÔNG THỂ khôi phục!', 'Vẫn Xóa', true)) return;
    if (!await showConfirm('XÁC NHẬN LẦN CUỐI', 'BẠN THỰC SỰ MUỐN XÓA TÀI KHOẢN NÀY?', 'XÓA', true)) return;

    const res = await apiCall('/security/actions.php?action=delete_account', 'POST');
    if (res.status === 200 && res.data.success) {
        await showDialog('Thành công', 'Tài khoản của bạn đã bị xóa vĩnh viễn khỏi hệ thống.', 'success');
        logout();
    } else {
        await showDialog('Lỗi', res.data.error || 'Không thể xóa tài khoản', 'error');
    }
}

async function loadHistory() {
    const container = document.getElementById('history-list');
    container.innerHTML = '<div class="text-muted text-sm italic">Đang tải lịch sử...</div>';

    const res = await apiCall('/security/actions.php?action=get_history');
    if (res.status === 200 && res.data.success) {
        if (res.data.data.length === 0) {
            container.innerHTML = '<div class="text-muted text-sm italic text-center py-8">Chưa ghi nhận lịch sử đăng nhập nào.</div>';
            return;
        }
        container.innerHTML = res.data.data.slice(0, 10).map(log => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-primary shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    </div>
                    <div>
                        <div class="font-bold text-dark text-sm">${log.device}</div>
                        <div class="text-muted text-[10px]">${log.ip}</div>
                    </div>
                </div>
                <div class="text-muted text-xs">${log.time}</div>
            </div>
        `).join('');
    }
}

// Tự động chạy khi HTML đã được load xong
document.addEventListener('DOMContentLoaded', () => {
    switchTab('password'); // Khởi tạo tab mặc định
    loadUserData();
});
