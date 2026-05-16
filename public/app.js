const API_BASE = '../api';

let token = localStorage.getItem('token');
let currentUser = null;
let currentTodos = [];

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
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('tab-login').classList.add('active');
    document.getElementById('tab-register').classList.remove('active');
}

function showRegister() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
    document.getElementById('tab-login').classList.remove('active');
    document.getElementById('tab-register').classList.add('active');
}

function showTodoSection() {
    document.getElementById('auth-section').style.display = 'none';
    document.getElementById('todo-section').style.display = 'block';
}

function showAuthSection() {
    document.getElementById('auth-section').style.display = 'block';
    document.getElementById('todo-section').style.display = 'none';
}

// API Helpers
async function apiCall(endpoint, method = 'GET', body = null) {
    const headers = {
        'Content-Type': 'application/json'
    };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = {
        method,
        headers
    };
    if (body) {
        options.body = JSON.stringify(body);
    }

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
        errorDiv.textContent = data.error || 'Login failed';
    }
}

async function register() {
    const username = document.getElementById('reg-username').value;
    const email = document.getElementById('reg-email').value;
    const password = document.getElementById('reg-password').value;
    const errorDiv = document.getElementById('reg-error');
    const successDiv = document.getElementById('reg-success');

    errorDiv.textContent = '';
    successDiv.textContent = '';

    const { status, data } = await apiCall('/auth/register.php', 'POST', { username, email, password });

    if (status === 201 || (status === 200 && data.message)) {
        successDiv.textContent = 'Registration successful! Please login.';
        setTimeout(showLogin, 2000);
    } else {
        errorDiv.textContent = data.error || 'Registration failed';
    }
}

function logout() {
    token = null;
    currentUser = null;
    localStorage.removeItem('token');
    showAuthSection();
    showLogin();
}

// Todo Actions
async function fetchTodos() {
    const { status, data } = await apiCall('/index.php', 'GET');
    
    if (status === 200) {
        currentUser = data.user;
        currentTodos = data.todos;
        document.getElementById('user-info').textContent = `Logged in as: ${currentUser.email}`;
        renderTodos();
    } else {
        if (status === 401) {
            logout(); // Token expired or invalid
        } else {
            alert('Failed to fetch todos: ' + (data.error || 'Unknown error'));
        }
    }
}

function renderTodos() {
    const listDiv = document.getElementById('todos-list');
    listDiv.innerHTML = '';

    if (!currentTodos || currentTodos.length === 0) {
        listDiv.innerHTML = '<p>No todos found. Add one above!</p>';
        return;
    }

    currentTodos.forEach(todo => {
        const card = document.createElement('div');
        card.className = `todo-card ${todo.status === 'completed' ? 'completed' : ''}`;
        
        card.innerHTML = `
            <div class="todo-content">
                <h4>${todo.title}</h4>
                ${todo.description ? `<p>${todo.description}</p>` : ''}
                <div class="todo-meta">
                    <span class="status-badge status-${todo.status}">${todo.status}</span>
                    <span class="priority-badge priority-${todo.priority}">${todo.priority}</span>
                    <small>Created: ${new Date(todo.created_at).toLocaleString()}</small>
                </div>
            </div>
            <div class="todo-actions">
                <button onclick="openEditModal(${todo.id})" class="btn-secondary" style="background:#2196F3">Edit</button>
                <button onclick="deleteTodo(${todo.id})" class="btn-secondary">Delete</button>
            </div>
        `;
        listDiv.appendChild(card);
    });
}

async function createTodo() {
    const titleInput = document.getElementById('new-todo-title');
    const descInput = document.getElementById('new-todo-desc');
    
    const title = titleInput.value.trim();
    const description = descInput.value.trim();

    if (!title) {
        alert('Title is required');
        return;
    }

    const { status, data } = await apiCall('/todos/create.php', 'POST', { title, description });

    if (status === 200) {
        titleInput.value = '';
        descInput.value = '';
        fetchTodos();
    } else {
        alert('Failed to create todo: ' + data.error);
    }
}

async function deleteTodo(id) {
    if (!confirm('Are you sure you want to delete this todo?')) return;

    const { status, data } = await apiCall('/todos/delete.php', 'DELETE', { id });

    if (status === 200) {
        fetchTodos();
    } else {
        alert('Failed to delete todo: ' + data.error);
    }
}

// Edit Modal Handling
function openEditModal(id) {
    const todo = currentTodos.find(t => t.id == id);
    if (!todo) return;

    document.getElementById('edit-todo-id').value = todo.id;
    document.getElementById('edit-todo-title').value = todo.title;
    document.getElementById('edit-todo-desc').value = todo.description || '';
    document.getElementById('edit-todo-status').value = todo.status || 'pending';
    document.getElementById('edit-todo-priority').value = todo.priority || 'medium';

    document.getElementById('edit-modal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

async function updateTodo() {
    const id = document.getElementById('edit-todo-id').value;
    const title = document.getElementById('edit-todo-title').value.trim();
    const description = document.getElementById('edit-todo-desc').value.trim();
    const status = document.getElementById('edit-todo-status').value;
    const priority = document.getElementById('edit-todo-priority').value;

    if (!title) {
        alert('Title is required');
        return;
    }

    const { status: reqStatus, data } = await apiCall('/todos/update.php', 'PUT', {
        id, title, description, status, priority
    });

    if (reqStatus === 200) {
        closeEditModal();
        fetchTodos();
    } else {
        alert('Failed to update todo: ' + data.error);
    }
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('edit-modal');
    if (event.target == modal) {
        closeEditModal();
    }
}
