// Tự động tìm gốc của API dựa trên cấu trúc URL
const getApiBase = () => {
    const origin = window.location.origin;
    const path = window.location.pathname;
    
    // Nếu URL có chứa /TodoList/ (XAMPP), dùng /TodoList/api
    if (path.toLowerCase().indexOf('/todolist/') !== -1) {
        return origin + '/TodoList/api';
    }
    // Nếu không (Docker hoặc đã cấu hình Domain), dùng /api trực tiếp
    return origin + '/api';
};

// Tự động tìm gốc của giao diện (Public)
const getProjectRoot = () => {
    const origin = window.location.origin;
    const path = window.location.pathname;
    if (path.toLowerCase().indexOf('/todolist/') !== -1) {
        return origin + '/TodoList/public';
    }
    return origin + '/public';
};

const API_BASE = getApiBase();
const PROJECT_ROOT = getProjectRoot();

// Token Management
function getToken() {
    return localStorage.getItem('token') || sessionStorage.getItem('token');
}

function setToken(token, rememberMe) {
    if (rememberMe) {
        localStorage.setItem('token', token);
        sessionStorage.removeItem('token');
    } else {
        sessionStorage.setItem('token', token);
        localStorage.removeItem('token');
    }
}

function removeToken() {
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');
}

// Kiểm tra quyền Admin (bao gồm cả superadmin)
function isAdmin() {
    const token = getToken();
    if (!token) return false;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.role === 'admin' || payload.role === 'superadmin';
    } catch (e) {
        return false;
    }
}

// Kiểm tra quyền Superadmin
function isSuperAdmin() {
    const token = getToken();
    if (!token) return false;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.role === 'superadmin';
    } catch (e) {
        return false;
    }
}

// API Call Wrapper
async function apiCall(endpoint, method = 'GET', body = null) {
    const token = getToken();
    const headers = {
        'Content-Type': 'application/json'
    };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = { method, headers };
    if (body) options.body = JSON.stringify(body);

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        
        let data;
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            return { status: response.status, data: { error: 'Server returned non-JSON response' } };
        }
        
        // Handle 401 Unauthorized globally
        if (response.status === 401 && window.location.pathname.indexOf('/auth/') === -1) {
            removeToken();
            window.location.href = PROJECT_ROOT + '/auth/';
        }
        
        return { status: response.status, data };
    } catch (error) {
        console.error('API Error:', error);
        return { status: 500, data: { error: `Connection error: ${error.message}` } };
    }
}

// Global Auth Guards
function requireAuth() {
    if (!getToken()) {
        window.location.href = PROJECT_ROOT + '/auth/';
    }
}

function requireGuest() {
    if (getToken()) {
        window.location.href = PROJECT_ROOT + '/main/';
    }
}

function logout() {
    removeToken();
    localStorage.removeItem('currentUser');
    window.location.href = PROJECT_ROOT + '/auth/';
}
