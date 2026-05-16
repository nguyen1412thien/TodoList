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
