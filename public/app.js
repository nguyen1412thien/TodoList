const API_BASE = '../api';

let token = localStorage.getItem('token');
let currentUser = null;
let currentTodos = [];
let currentFilter = 'all';

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    if (token) {
        showTodoSection();
        fetchTodos();
    } else {
        showLogin();
    }
});

// UI Toggles
function showLogin() {
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('register-form').classList.add('hidden');
    
    document.getElementById('tab-login').className = "flex-1 tab-btn font-bold active";
    document.getElementById('tab-register').className = "flex-1 tab-btn font-bold";
}

function showRegister() {
    document.getElementById('login-form').classList.add('hidden');
    document.getElementById('register-form').classList.remove('hidden');
    
    document.getElementById('tab-login').className = "flex-1 tab-btn font-bold";
    document.getElementById('tab-register').className = "flex-1 tab-btn font-bold active";
}

function showTodoSection() {
    document.getElementById('auth-section').classList.add('hidden');
    document.getElementById('todo-section').classList.remove('hidden');
}

function showAuthSection() {
    document.getElementById('auth-section').classList.remove('hidden');
    document.getElementById('todo-section').classList.add('hidden');
}

// API Helpers
async function apiCall(endpoint, method = 'GET', body = null) {
    const headers = { 'Content-Type': 'application/json' };
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const options = { method, headers };
    if (body) options.body = JSON.stringify(body);

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const data = await response.json();
        return { status: response.status, data };
    } catch (error) {
        console.error('API Error:', error);
        return { status: 500, data: { error: 'Network error' } };
    }
}

// Auth Actions
async function login() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const errorDiv = document.getElementById('login-error');
    errorDiv.textContent = '';

    const { status, data } = await apiCall('/auth/login.php', 'POST', { email, password });

    if (status === 200 && data.token) {
        token = data.token;
        localStorage.setItem('token', token);
        showTodoSection();
        fetchTodos();
    } else {
        errorDiv.textContent = data.error || 'Đăng nhập thất bại';
    }
}

async function register() {
    const name = document.getElementById('reg-name').value;
    const username = document.getElementById('reg-username').value;
    const email = document.getElementById('reg-email').value;
    const password = document.getElementById('reg-password').value;
    const errorDiv = document.getElementById('reg-error');
    const successDiv = document.getElementById('reg-success');

    errorDiv.textContent = '';
    successDiv.textContent = '';

    const { status, data } = await apiCall('/auth/register.php', 'POST', { name, username, email, password });

    if (status === 201 || (status === 200 && data.message)) {
        successDiv.textContent = 'Đăng ký thành công! Đang chuyển sang đăng nhập...';
        setTimeout(() => {
            document.getElementById('login-email').value = email;
            showLogin();
            successDiv.textContent = '';
        }, 1500);
    } else {
        errorDiv.textContent = data.error || 'Đăng ký thất bại';
    }
}

function logout() {
    token = null;
    currentUser = null;
    localStorage.removeItem('token');
    showAuthSection();
    showLogin();
}

function goToProfile() {
    // Placeholder function for navigation to the user profile page.
    // Replace this with actual navigation logic when the profile page is built.
    alert("Chức năng đang được phát triển. Sẽ chuyển hướng đến trang tài khoản cá nhân của: " + (currentUser.name || currentUser.username));
}

// Todo Priority UI handling
function setNewPriority(priority, btnElement) {
    document.getElementById('new-priority-value').value = priority;
    
    // Reset all buttons
    const btns = document.querySelectorAll('#new-priority-btns .priority-btn');
    btns.forEach(b => {
        b.className = "btn priority-btn flex-1";
    });
    
    // Set active button
    if (btnElement) btnElement.className = `btn priority-btn flex-1 priority-${priority} active`;
}

function setEditPriority(priority, btnElement) {
    document.getElementById('edit-todo-priority').value = priority;
    
    // Reset all buttons
    const btns = document.querySelectorAll('#edit-priority-btns .priority-btn');
    btns.forEach(b => {
        b.className = "btn priority-btn flex-1";
    });
    
    // Set active button
    if (btnElement) btnElement.className = `btn priority-btn flex-1 priority-${priority} active`;
}

// Todo Filtering
function setFilter(filterType, btnElement) {
    currentFilter = filterType;
    
    // Reset all filter tabs
    const btns = document.querySelectorAll('.filter-btn');
    btns.forEach(b => {
        b.className = "btn filter-btn";
    });
    
    // Set active tab
    btnElement.className = "btn filter-btn active";
    
    renderTodos();
}

// Utility to get priority badge HTML
function getPriorityBadge(priority) {
    let label = 'Vừa';
    if (priority === 'low') label = 'Thấp';
    if (priority === 'high') label = 'Cao';
    return `<span class="badge badge-${priority}">${label}</span>`;
}

// Enter key submit
function handleKeyPress(e) {
    if (e.key === 'Enter') {
        createTodo();
    }
}

// Todo Actions
async function fetchTodos() {
    const { status, data } = await apiCall('/index.php', 'GET');
    
    if (status === 200) {
        currentUser = data.user;
        currentTodos = data.todos;
        
        let usernameDisplay = currentUser.name || currentUser.username;
        document.getElementById('user-info').textContent = `Chào, ${usernameDisplay}`;
        
        renderTodos();
    } else {
        if (status === 401) {
            logout();
        } else {
            console.error('Failed to fetch todos');
        }
    }
}

function renderTodos() {
    const listDiv = document.getElementById('todos-list');
    listDiv.innerHTML = '';

    let filteredTodos = currentTodos;
    if (currentFilter !== 'all') {
        filteredTodos = currentTodos.filter(t => t.status === currentFilter);
    }

    // Update Stats
    const activeCount = currentTodos.filter(t => t.status !== 'completed').length;
    const doneCount = currentTodos.filter(t => t.status === 'completed').length;
    const totalCount = currentTodos.length;
    const progress = totalCount === 0 ? 0 : Math.round((doneCount / totalCount) * 100);

    document.getElementById('active-count').textContent = activeCount;
    document.getElementById('done-count').textContent = doneCount;
    document.getElementById('progress-text').textContent = `${progress}%`;

    if (filteredTodos.length === 0) {
        listDiv.innerHTML = '<div class="text-center text-muted text-sm font-medium pt-8">Không có công việc nào.</div>';
        return;
    }

    filteredTodos.forEach(todo => {
        const priorityHtml = getPriorityBadge(todo.priority);
        const isCompleted = todo.status === 'completed';
        
        let dueDateHtml = '';
        if (todo.due_date) {
            let dateStr = todo.due_date;
            if (dateStr.includes(' ')) dateStr = dateStr.replace(' ', 'T');
            const dateObj = new Date(dateStr);
            const formattedDate = dateObj.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' });
            
            const isOverdue = !isCompleted && dateObj < new Date();
            
            if (isOverdue) {
                dueDateHtml = `<span class="badge badge-date badge-overdue"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Quá hạn: ${formattedDate}</span>`;
            } else {
                dueDateHtml = `<span class="badge badge-date"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ${formattedDate}</span>`;
            }
        }
        
        const completedClass = isCompleted ? 'completed' : '';
        const checkedClass = isCompleted ? 'checked' : '';
        const svgCheck = isCompleted ? `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>` : '';
        const targetStatus = isCompleted ? 'pending' : 'completed';

        listDiv.innerHTML += `
            <div class="flex items-center gap-4 todo-item ${completedClass}">
                <div onclick="toggleStatus(${todo.id}, '${targetStatus}')" class="checkbox-circle ${checkedClass}">${svgCheck}</div>
                <div class="flex-grow" style="cursor: pointer;" onclick="openEditModal(${todo.id})">
                    <p class="todo-title">${todo.title}</p>
                    <div class="flex items-center gap-2 mt-1" style="flex-wrap: wrap;">
                        ${priorityHtml}
                        ${dueDateHtml}
                        ${todo.description ? `<span class="text-xs text-muted" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">- ${todo.description}</span>` : ''}
                    </div>
                </div>
                <button onclick="deleteTodo(${todo.id})" class="btn btn-delete">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        `;
    });
}

// Toggle Description
function toggleDescription() {
    const container = document.getElementById('new-todo-desc-container');
    const btn = document.getElementById('desc-toggle-btn');

    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        btn.classList.add('active');
    } else {
        container.classList.add('hidden');
        btn.classList.remove('active');
    }
}

async function createTodo() {
    const titleInput = document.getElementById('new-todo-title');
    const priorityInput = document.getElementById('new-priority-value');
    const dueDateInput = document.getElementById('new-todo-duedate');
    const descInput = document.getElementById('new-todo-desc');
    
    const title = titleInput.value.trim();
    const priority = priorityInput.value;
    const statusVal = 'pending'; // Default status for new todo
    const dueDate = dueDateInput.value || null;
    const isDescVisible = !document.getElementById('new-todo-desc-container').classList.contains('hidden');
    const description = isDescVisible ? descInput.value.trim() : "";

    if (!title) return;

    // Fast UI update (Optimistic)
    const tempId = Date.now();
    currentTodos.unshift({
        id: tempId,
        title: title,
        description: description,
        status: statusVal,
        priority: priority,
        due_date: dueDate,
        created_at: new Date().toISOString()
    });
    renderTodos();
    
    // Reset inputs
    titleInput.value = '';
    descInput.value = '';
    dueDateInput.value = '';
    document.getElementById('new-todo-duedate-text').textContent = "Chọn ngày giờ";

    const { status, data } = await apiCall('/todos/create.php', 'POST', { 
        title, 
        priority,
        status: statusVal,
        due_date: dueDate,
        description
    });

    if (status === 200) {
        fetchTodos(); // Refresh for real ID
    } else {
        alert('Lỗi: ' + data.error);
        currentTodos = currentTodos.filter(t => t.id !== tempId);
        renderTodos();
    }
}

async function deleteTodo(id) {
    if (!confirm('Xóa công việc này?')) return;

    // Fast UI Update
    currentTodos = currentTodos.filter(t => t.id !== id);
    renderTodos();

    const { status } = await apiCall('/todos/delete.php', 'DELETE', { id });
    if (status !== 200) fetchTodos(); // Revert on error
}

async function toggleStatus(id, newStatus) {
    const todo = currentTodos.find(t => t.id === id);
    if (!todo) return;

    // Fast UI update
    const oldStatus = todo.status;
    todo.status = newStatus;
    renderTodos();

    const { status } = await apiCall('/todos/update.php', 'PUT', {
        id: todo.id,
        title: todo.title,
        description: todo.description,
        status: newStatus,
        priority: todo.priority,
        due_date: todo.due_date
    });

    if (status !== 200) {
        todo.status = oldStatus; // Revert
        renderTodos();
    }
}

// Edit Modal Handling
function openEditModal(id) {
    const todo = currentTodos.find(t => t.id === id);
    if (!todo) return;

    document.getElementById('edit-todo-id').value = todo.id;
    document.getElementById('edit-todo-title').value = todo.title;
    document.getElementById('edit-todo-desc').value = todo.description || '';
    
    const priority = todo.priority || 'medium';
    const btn = document.querySelector(`#edit-priority-btns [data-value="${priority}"]`);
    setEditPriority(priority, btn);

    const dueDateInput = document.getElementById('edit-todo-duedate');
    const dueDateText = document.getElementById('edit-todo-duedate-text');
    if (todo.due_date) {
        dueDateInput.value = todo.due_date;
        let dateStr = todo.due_date;
        if (dateStr.includes(' ')) dateStr = dateStr.replace(' ', 'T');
        const d = new Date(dateStr);
        dueDateText.textContent = d.toLocaleString('vi-VN', { 
            hour: '2-digit', minute: '2-digit', 
            day: '2-digit', month: '2-digit', year: 'numeric' 
        });
    } else {
        dueDateInput.value = '';
        dueDateText.textContent = 'Chọn ngày giờ';
    }

    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

async function updateTodo() {
    const id = parseInt(document.getElementById('edit-todo-id').value);
    const title = document.getElementById('edit-todo-title').value.trim();
    const description = document.getElementById('edit-todo-desc').value.trim();
    const existingTodo = currentTodos.find(t => t.id === id);
    const status = existingTodo ? existingTodo.status : 'pending';
    const priority = document.getElementById('edit-todo-priority').value;
    const due_date = document.getElementById('edit-todo-duedate').value || null;

    if (!title) {
        alert('Vui lòng nhập tiêu đề');
        return;
    }

    // Fast UI update
    const todoIndex = currentTodos.findIndex(t => t.id === id);
    if (todoIndex > -1) {
        currentTodos[todoIndex] = { ...currentTodos[todoIndex], title, description, status, priority, due_date };
        renderTodos();
    }
    
    closeEditModal();

    const { status: reqStatus, data } = await apiCall('/todos/update.php', 'PUT', {
        id, title, description, status, priority, due_date
    });

    if (reqStatus !== 200) {
        alert('Lỗi cập nhật: ' + data.error);
        fetchTodos();
    }
}

// Close modal if clicked outside
window.onclick = function(event) {
    const editModal = document.getElementById('edit-modal');
    const datetimeModal = document.getElementById('datetime-modal');
    
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == datetimeModal) {
        closeDatetimeModal();
    }
}

// ================= Datetime Modal Logic =================
let currentDatetimeTarget = 'new';

function openDatetimeModal(target = 'new') {
    currentDatetimeTarget = target;
    const prefix = target === 'edit' ? 'edit-todo' : 'new-todo';
    const hiddenInput = document.getElementById(`${prefix}-duedate`);
    const dateInput = document.getElementById('modal-date');
    const hourInput = document.getElementById('modal-hour');
    const minInput = document.getElementById('modal-minute');
    const formatToggle = document.getElementById('modal-time-format');

    let d = new Date();
    d.setHours(d.getHours() + 1);
    d.setMinutes(0);

    if (hiddenInput.value) {
        let dateStr = hiddenInput.value;
        if (dateStr.includes(' ')) dateStr = dateStr.replace(' ', 'T');
        d = new Date(dateStr);
    }

    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    dateInput.value = `${yyyy}-${mm}-${dd}`;

    let hours = d.getHours();
    let minutes = d.getMinutes();
    const is24h = formatToggle.value === '24';

    if (!is24h) {
        const ampm = hours >= 12 ? 'PM' : 'AM';
        setAmPm(ampm);
        hours = hours % 12;
        hours = hours ? hours : 12;
    }

    hourInput.value = String(hours).padStart(2, '0');
    minInput.value = String(minutes).padStart(2, '0');

    document.getElementById('datetime-modal').classList.remove('hidden');
}

function closeDatetimeModal() {
    document.getElementById('datetime-modal').classList.add('hidden');
}

function toggleTimeFormat() {
    const btn = document.getElementById('time-format-toggle');
    const formatInput = document.getElementById('modal-time-format');
    const ampmContainer = document.getElementById('ampm-toggle-container');
    const hourInput = document.getElementById('modal-hour');
    const label12h = document.getElementById('format-label-12h');
    const label24h = document.getElementById('format-label-24h');
    
    let hours = parseInt(hourInput.value) || 0;
    
    if (formatInput.value === '12') {
        btn.classList.add('active');
        formatInput.value = '24';
        ampmContainer.style.display = 'none';
        
        label12h.style.color = 'var(--text-muted)';
        label24h.style.color = 'var(--text-main)';
        
        const ampm = document.getElementById('modal-ampm').value;
        if (ampm === 'PM' && hours < 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;
        
        hourInput.min = "0";
        hourInput.max = "23";
        hourInput.placeholder = "23";
        hourInput.value = String(hours).padStart(2, '0');
    } else {
        btn.classList.remove('active');
        formatInput.value = '12';
        ampmContainer.style.display = 'flex';
        
        label12h.style.color = 'var(--text-main)';
        label24h.style.color = 'var(--text-muted)';
        
        const ampm = hours >= 12 ? 'PM' : 'AM';
        setAmPm(ampm);
        
        hours = hours % 12;
        hours = hours ? hours : 12;
        
        hourInput.min = "1";
        hourInput.max = "12";
        hourInput.placeholder = "12";
        hourInput.value = String(hours).padStart(2, '0');
    }
}

function setAmPm(value) {
    document.getElementById('modal-ampm').value = value;
    document.getElementById('btn-am').className = `tab-btn font-bold ${value === 'AM' ? 'active' : ''}`;
    document.getElementById('btn-pm').className = `tab-btn font-bold ${value === 'PM' ? 'active' : ''}`;
}

function validateTimeInput(input, type) {
    let val = parseInt(input.value);
    if (isNaN(val)) return;
    
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    
    if (val > max) input.value = max;
    if (val < min) input.value = min;
}

function formatTimeInput(input) {
    if (input.value !== "") {
        input.value = String(input.value).padStart(2, '0');
    }
}

function saveDatetime() {
    const prefix = currentDatetimeTarget === 'edit' ? 'edit-todo' : 'new-todo';
    const dateInput = document.getElementById('modal-date').value;
    let hours = parseInt(document.getElementById('modal-hour').value) || 0;
    const minutes = parseInt(document.getElementById('modal-minute').value) || 0;
    const is24h = document.getElementById('modal-time-format').value === '24';
    
    if (!dateInput) {
        alert('Vui lòng chọn ngày!');
        return;
    }

    if (!is24h) {
        const ampm = document.getElementById('modal-ampm').value;
        if (ampm === 'PM' && hours < 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;
    }

    const d = new Date(dateInput + "T00:00:00");
    d.setHours(hours);
    d.setMinutes(minutes);
    d.setSeconds(0);

    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    const ss = String(d.getSeconds()).padStart(2, '0');
    const localDateTimeStr = `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;

    document.getElementById(`${prefix}-duedate`).value = localDateTimeStr;

    const formattedDate = d.toLocaleString('vi-VN', { 
        hour: '2-digit', minute: '2-digit', 
        day: '2-digit', month: '2-digit', year: 'numeric' 
    });
    document.getElementById(`${prefix}-duedate-text`).textContent = formattedDate;

    closeDatetimeModal();
}

function clearDatetime() {
    const prefix = currentDatetimeTarget === 'edit' ? 'edit-todo' : 'new-todo';
    document.getElementById(`${prefix}-duedate`).value = "";
    document.getElementById(`${prefix}-duedate-text`).textContent = "Chọn ngày giờ";
    closeDatetimeModal();
}