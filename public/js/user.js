// Profile Logic
function showProfile() {
    document.getElementById('todo-section').classList.add('hidden');
    document.getElementById('profile-section').classList.remove('hidden');
    
    // Set user profile data
    const name = currentUser.name || currentUser.username;
    document.getElementById('profile-user-name').textContent = name;
    
    // Count completed tasks
    const completedTasks = currentTodos.filter(t => t.status === 'completed').length;
    document.getElementById('profile-completed-count').textContent = completedTasks;
    
    // Simulate user join date
    document.getElementById('profile-user-date').textContent = "Thành viên từ " + new Date().toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' });
}

function hideProfile() {
    document.getElementById('profile-section').classList.add('hidden');
    document.getElementById('todo-section').classList.remove('hidden');
}

// Update widget profile name
function updateWidgetProfile() {
    if (currentUser) {
        document.getElementById('widget-user-name').textContent = currentUser.name || currentUser.username;
    }
}
