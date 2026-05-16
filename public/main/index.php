<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenTask - Công việc</title>
    <link rel="stylesheet" href="../style.css?v=7">
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
<body>
    <div id="app" class="container">

        <div id="todo-section">
            <!-- Top Bar -->
            <div class="flex justify-between items-center mb-10 gap-4" style="flex-wrap: wrap;">
                <!-- Header -->
                <header>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="logo-icon logo-icon-sm flex items-center justify-center text-white bg-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                        <h1 class="font-bold tracking-widest text-dark" style="font-size: 1.875rem; letter-spacing: -0.025em;">ZenTask</h1>
                    </div>
                </header>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Notification Bell -->
                    <button class="btn-icon btn-white card-shadow text-muted" onclick="openNotificationModal()" style="width: 3.5rem; height: 3.5rem; border-radius: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    </button>
                    
                    <!-- User Profile Widget (Trigger) -->
                    <div class="profile-widget card-shadow inline-flex items-center" onclick="window.location.href='../user/'" style="padding: 0.75rem 1.25rem; cursor: pointer;">
                        <div class="flex items-center gap-4">
                            <div class="avatar-container relative">
                                <div class="avatar-box bg-gradient-primary flex items-center justify-center text-white relative overflow-hidden" style="width: 3rem; height: 3rem; border-radius: 50%;">
                                    <img id="widget-user-avatar" src="" alt="Avatar" class="w-full h-full object-cover hidden absolute inset-0">
                                    <svg id="widget-default-avatar" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative z-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </div>
                                <div class="avatar-badge bg-success absolute flex items-center justify-center" style="width: 1.25rem; height: 1.25rem; border-radius: 50%; bottom: 0; right: 0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            </div>
                            <div>
                                <h2 id="widget-user-name" class="font-bold text-dark tracking-tight leading-none mb-1" style="font-size: 1rem;">Tên người dùng</h2>
                                <div class="flex items-center gap-2">
                                    <span class="badge-level font-bold text-primary bg-primary-light locked-feature-text" title="Sắp ra mắt" style="font-size: 0.5rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">Lv. 0</span>
                                    <span class="badge-streak font-bold text-muted locked-feature-text" title="Sắp ra mắt" style="font-size: 0.5rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem; background-color: #f3f4f6;">🔥 0 Day</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="card mb-8">
                <div class="input-group" style="position: relative;">
                    <input type="text" id="new-todo-title" placeholder="Bạn cần làm gì?" class="form-input form-input-lg" style="background-color: var(--bg-input);" onkeypress="handleKeyPress(event)">
                    <button onclick="createTodo()" class="btn btn-primary btn-icon btn-icon-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>
                
                <div class="grid-2 mb-4">
                    <!-- Priority -->
                    <div>
                        <label class="form-label">Độ ưu tiên</label>
                        <div class="flex gap-1 input-match-height" id="new-priority-btns">
                            <button type="button" onclick="setNewPriority('low', this)" class="btn priority-btn flex-1" data-value="low">Thấp</button>
                            <button type="button" onclick="setNewPriority('medium', this)" class="btn priority-btn flex-1 priority-medium active" data-value="medium">Vừa</button>
                            <button type="button" onclick="setNewPriority('high', this)" class="btn priority-btn flex-1" data-value="high">Cao</button>
                        </div>
                        <input type="hidden" id="new-priority-value" value="medium">
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="form-label">Hạn chót</label>
                        <button type="button" id="new-todo-duedate-display" class="form-input input-match-height text-left text-muted" onclick="openDatetimeModal('new')" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; background-color: var(--bg-card);">
                            <span id="new-todo-duedate-text">Chọn ngày giờ</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </button>
                        <input type="hidden" id="new-todo-duedate" value="">
                    </div>
                </div>

                <!-- Description Toggle -->
                <div class="pt-4" style="border-top: 1px solid var(--border-light); margin-top: 0.5rem;">
                    <div class="flex justify-between items-center mb-2">
                        <label class="form-label" style="margin: 0;">Thêm mô tả chi tiết</label>
                        <button id="desc-toggle-btn" type="button" onclick="toggleDescription()" class="toggle-switch">
                            <span id="desc-toggle-circle" class="toggle-circle"></span>
                        </button>
                    </div>
                    <div id="new-todo-desc-container" class="hidden">
                        <textarea id="new-todo-desc" placeholder="Nhập mô tả cho công việc này..." rows="3" class="form-input mt-2"></textarea>
                    </div>
                </div>
            </div>

            <!-- Filter & Stats -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex tabs-container" id="filter-tabs">
                    <button onclick="setFilter('all', this)" class="btn filter-btn active" data-filter="all">Tất cả</button>
                    <button onclick="setFilter('pending', this)" class="btn filter-btn" data-filter="pending">Đang làm</button>
                    <button onclick="setFilter('completed', this)" class="btn filter-btn" data-filter="completed">Đã xong</button>
                </div>
                <div class="text-right">
                    <span class="form-label">Tiến độ</span>
                    <span id="progress-text" class="text-sm font-bold text-dark">0%</span>
                </div>
            </div>

            <!-- List Items -->
            <div id="todos-list" class="flex flex-col gap-3">
                <!-- Todos will be injected here -->
            </div>

            <!-- Footer -->
            <footer class="flex justify-between items-center mt-20 pt-8" style="border-top: 1px solid var(--border-light);">
                <div class="flex gap-6">
                    <div>
                        <span class="form-label">Active</span>
                        <span id="active-count" class="text-sm font-bold text-dark">0</span>
                    </div>
                    <div>
                        <span class="form-label">Done</span>
                        <span id="done-count" class="text-sm font-bold text-dark">0</span>
                    </div>
                </div>
                <span class="form-label" style="margin: 0; color: #d1d5db;">PROJECT BY 14DEC</span>
            </footer>
        </div>
    
        
    <div id="edit-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <h2 class="modal-title font-bold text-main">Chỉnh sửa công việc</h2>
            
            <input type="hidden" id="edit-todo-id">
            
            <div class="input-group">
                <label class="form-label">Tiêu đề</label>
                <input type="text" id="edit-todo-title" class="form-input">
            </div>
            
            <div class="input-group">
                <label class="form-label">Mô tả (Tùy chọn)</label>
                <input type="text" id="edit-todo-desc" class="form-input">
            </div>

            <div class="mb-6 grid-2">
                <div>
                    <label class="form-label">Độ ưu tiên</label>
                    <div class="flex gap-1 input-match-height" id="edit-priority-btns">
                        <button type="button" onclick="setEditPriority('low', this)" class="btn priority-btn flex-1" data-value="low">Thấp</button>
                        <button type="button" onclick="setEditPriority('medium', this)" class="btn priority-btn flex-1" data-value="medium">Vừa</button>
                        <button type="button" onclick="setEditPriority('high', this)" class="btn priority-btn flex-1" data-value="high">Cao</button>
                    </div>
                    <input type="hidden" id="edit-todo-priority" value="medium">
                </div>
                
                <div>
                    <label class="form-label">Hạn chót</label>
                    <button type="button" id="edit-todo-duedate-display" class="form-input input-match-height text-left text-muted" onclick="openDatetimeModal('edit')" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; background-color: var(--bg-card);">
                        <span id="edit-todo-duedate-text">Chọn ngày giờ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </button>
                    <input type="hidden" id="edit-todo-duedate" value="">
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="closeEditModal()" class="btn btn-block btn-cancel">Hủy</button>
                <button onclick="updateTodo()" class="btn btn-block btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </div>

    
    <div id="datetime-modal" class="modal-overlay hidden">
        <div class="modal-content !max-w-sm">
            <div class="text-center mb-6">
                <div class="bg-primary/10 mx-auto mb-3 flex items-center justify-center text-primary" style="width: 3.5rem; height: 3.5rem; border-radius: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h2 class="modal-title font-bold text-main text-xl">Thời hạn công việc</h2>
            </div>
            
            <div class="mb-5 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <label class="form-label text-center mb-3">Ngày thực hiện</label>
                <input type="date" id="modal-date" class="form-input !w-full !p-3 text-center font-bold text-lg rounded-xl shadow-sm bg-white border-gray-200">
            </div>

            <div class="mb-6 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <div class="flex justify-between items-center mb-3 px-2">
                    <label class="form-label mb-0">Thời gian</label>
                    <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-lg shadow-sm border border-gray-100">
                        <span class="text-xs font-bold" id="format-label-12h" style="color: var(--text-main);">12h</span>
                        <button id="time-format-toggle" type="button" onclick="toggleTimeFormat()" class="toggle-switch transform scale-75 origin-center m-0">
                            <span class="toggle-circle"></span>
                        </button>
                        <span class="text-xs font-bold text-muted" id="format-label-24h">24h</span>
                    </div>
                </div>
                
                <div class="flex gap-3 justify-center items-center mt-2">
                    <input type="number" id="modal-hour" class="form-input text-center font-bold text-3xl !w-20 !h-16 shadow-sm !p-0 rounded-xl bg-white border-gray-200" min="1" max="12" placeholder="12" onchange="validateTimeInput(this, 'hour')" onblur="formatTimeInput(this)">
                    
                    <span class="font-bold text-2xl text-gray-400">:</span>
                    
                    <input type="number" id="modal-minute" class="form-input text-center font-bold text-3xl !w-20 !h-16 shadow-sm !p-0 rounded-xl bg-white border-gray-200" min="0" max="59" placeholder="00" onchange="validateTimeInput(this, 'minute')" onblur="formatTimeInput(this)">
                    
                    <div id="ampm-toggle-container" class="flex flex-col tabs-container bg-gray-100 p-1 rounded-xl shadow-inner gap-1 ml-2">
                        <button type="button" id="btn-am" class="tab-btn font-bold active text-xs px-3 py-1.5 rounded-lg" onclick="setAmPm('AM')">AM</button>
                        <button type="button" id="btn-pm" class="tab-btn font-bold text-xs px-3 py-1.5 rounded-lg" onclick="setAmPm('PM')">PM</button>
                        <input type="hidden" id="modal-ampm" value="AM">
                    </div>
                </div>
                <input type="hidden" id="modal-time-format" value="12">
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="closeDatetimeModal()" class="btn flex-1 btn-cancel bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl">Bỏ qua</button>
                <button onclick="saveDatetime()" class="btn flex-1 btn-primary py-3 rounded-xl">Xác nhận</button>
            </div>
            <button onclick="clearDatetime()" class="w-full mt-3 text-sm font-semibold text-danger hover:text-red-700 transition-colors py-2">Xóa trắng (Không giới hạn thời gian)</button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal-overlay hidden">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="bg-danger-light mx-auto mb-4 flex items-center justify-center text-danger" style="width: 4rem; height: 4rem; border-radius: 50%;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <h2 class="font-bold text-dark text-xl mb-2">Xóa công việc?</h2>
            <p class="text-muted text-sm mb-6">Bạn có chắc chắn muốn xóa công việc này không? Hành động này không thể hoàn tác.</p>
            <div class="flex gap-3">
                <button class="btn btn-cancel flex-1" onclick="closeDeleteModal()">Hủy</button>
                <button class="btn btn-danger flex-1" onclick="confirmDelete()">Xóa</button>
            </div>
        </div>
    </div>

    <!-- Notification Popup Modal -->
    <div id="notification-modal" class="modal-overlay hidden">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="bg-primary-light mx-auto mb-4 flex items-center justify-center text-primary" style="width: 4rem; height: 4rem; border-radius: 50%;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            </div>
            <h2 class="font-bold text-dark text-xl mb-2">Tính năng đang phát triển</h2>
            <p class="text-muted text-sm mb-6">Trung tâm thông báo đang được xây dựng và sẽ sớm ra mắt trong bản cập nhật tới.</p>
            <button class="btn btn-primary btn-block py-3 rounded-xl font-bold" onclick="closeNotificationModal()">Đóng</button>
        </div>
    </div>
    
    </div>
    <script src="../js/api.js?v=<?php echo filemtime('../js/api.js'); ?>"></script>
    <script src="../js/dialog.js?v=<?php echo filemtime('../js/dialog.js'); ?>"></script>
    <script src="../js/main.js?v=<?php echo filemtime('../js/main.js'); ?>"></script>
    <script>
        requireAuth();
        document.addEventListener('DOMContentLoaded', fetchTodos);
    </script>
</body>
</html>