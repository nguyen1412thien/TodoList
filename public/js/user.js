// Profile Logic
function showProfile() {
    const todoSection = document.getElementById('todo-section');
    const profileSection = document.getElementById('profile-section');
    
    if (todoSection) todoSection.classList.add('hidden');
    if (profileSection) profileSection.classList.remove('hidden');
    
    // Set user profile data
    const name = currentUser.name;
    const profileNameEl = document.getElementById('profile-user-name');
    if (profileNameEl) profileNameEl.textContent = name;
    
    // Count completed tasks
    const completedTasks = currentTodos.filter(t => t.status === 'completed').length;
    const completedCountEl = document.getElementById('profile-completed-count');
    if (completedCountEl) completedCountEl.textContent = completedTasks;
    
    // Simulate user join date
    const dateEl = document.getElementById('profile-user-date');
    if (dateEl) dateEl.textContent = "Thành viên từ " + new Date().toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' });
    
    // Recent Activity
    const recentListDiv = document.getElementById('recent-activity-list');
    if (recentListDiv) {
        recentListDiv.innerHTML = '';
        const recentTasks = currentTodos.slice(0, 4);
        
        if (recentTasks.length === 0) {
            recentListDiv.innerHTML = '<div class="text-center text-muted text-sm font-medium py-4">Chưa có hoạt động nào.</div>';
        } else {
            recentTasks.forEach(todo => {
                recentListDiv.innerHTML += createActivityItemHtml(todo);
            });
            
            if (currentTodos.length > 4) {
                recentListDiv.innerHTML += `
                    <div class="text-center mt-2">
                        <button onclick="openAllActivitiesModal()" class="btn btn-white text-muted hover-bg-light" style="border-radius: 9999px; padding: 0.5rem 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            <span class="ml-2 font-bold text-xs uppercase tracking-wider" style="vertical-align:middle; margin-left:0.5rem;">Xem thêm (${currentTodos.length - 4})</span>
                        </button>
                    </div>
                `;
            }
        }
    }
}

function createActivityItemHtml(todo) {
    const isCompleted = todo.status === 'completed';
    const completedClass = isCompleted ? 'completed' : '';
    const checkedClass = isCompleted ? 'checked' : '';
    const svgCheck = isCompleted ? `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>` : '';
    
    let priorityClass = 'badge-low';
    let priorityText = 'Thấp';
    if (todo.priority === 'medium') { priorityClass = 'badge-medium'; priorityText = 'Vừa'; }
    if (todo.priority === 'high') { priorityClass = 'badge-high'; priorityText = 'Cao'; }
    const priorityHtml = `<span class="badge ${priorityClass}">${priorityText}</span>`;
    
    return `
        <div class="todo-item ${completedClass}" style="pointer-events: none; margin-bottom: 0.75rem;">
            <div class="flex items-center gap-4">
                <div class="checkbox-circle ${checkedClass}">${svgCheck}</div>
                <div class="flex-grow">
                    <p class="todo-title mb-1">${todo.title}</p>
                    <div class="flex items-center gap-2">
                        ${priorityHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function openAllActivitiesModal() {
    const modal = document.getElementById('all-activities-modal');
    const listDiv = document.getElementById('all-activities-list');
    
    if (modal && listDiv) {
        listDiv.innerHTML = '';
        currentTodos.forEach(todo => {
            listDiv.innerHTML += createActivityItemHtml(todo);
        });
        modal.classList.remove('hidden');
    }
}

function closeAllActivitiesModal() {
    const modal = document.getElementById('all-activities-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function hideProfile() {
    const todoSection = document.getElementById('todo-section');
    const profileSection = document.getElementById('profile-section');
    
    if (profileSection) profileSection.classList.add('hidden');
    if (todoSection) todoSection.classList.remove('hidden');
}

// Update widget profile name
function updateWidgetProfile() {
    if (currentUser) {
        document.getElementById('widget-user-name').textContent = currentUser.name;
    }
}
