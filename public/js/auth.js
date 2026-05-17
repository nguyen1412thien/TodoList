// Handle Tabs
function showLogin() {
    document.getElementById('tab-login').classList.add('active');
    document.getElementById('tab-register').classList.remove('active');
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('register-form').classList.add('hidden');
}

function showRegister() {
    document.getElementById('tab-register').classList.add('active');
    document.getElementById('tab-login').classList.remove('active');
    document.getElementById('register-form').classList.remove('hidden');
    document.getElementById('login-form').classList.add('hidden');
}

// Auth Actions
async function login() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const rememberMe = document.getElementById('remember-me').checked;
    const errorDiv = document.getElementById('login-error');
    errorDiv.textContent = '';

    const { status, data } = await apiCall('/auth/login.php', 'POST', { email, password });

    if (status === 200 && data.token) {
        setToken(data.token, rememberMe);

        // redirect to main page
        window.location.href = '../main/';
    } else if (status === 403) {        showDialog('Thông báo', data.error || 'Tài khoản của bạn đã bị khóa.', 'error');
        errorDiv.textContent = '';
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

// Enable Enter key on login
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('login-password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') login();
    });
});
