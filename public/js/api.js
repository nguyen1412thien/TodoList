// Sử dụng đường dẫn tương đối để tự động tương thích với mọi môi trường (IP, Localhost, Docker)
const API_BASE = '../../api';

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
        const data = await response.json();
        
        // Handle 401 Unauthorized globally
        if (response.status === 401 && window.location.pathname.indexOf('/auth/') === -1) {
            removeToken();
            window.location.href = '../auth/';
        }
        
        return { status: response.status, data };
    } catch (error) {
        console.error('API Error:', error);
        return { status: 500, data: { error: 'Network error' } };
    }
}

// Global Auth Guards
function requireAuth() {
    if (!getToken()) {
        window.location.href = '../auth/';
    }
}

function requireGuest() {
    if (getToken()) {
        window.location.href = '../main/';
    }
}

function logout() {
    removeToken();
    localStorage.removeItem('currentUser');
    window.location.href = '../auth/';
}
