// Hệ thống Custom Dialog & Confirm
// Cần Tailwind CSS để hiển thị đúng style

function createDialogOverlay() {
    let overlay = document.getElementById('zt-dialog-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'zt-dialog-overlay';
        overlay.className = 'fixed inset-0 z-[9999] bg-main/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300';
        
        const content = document.createElement('div');
        content.id = 'zt-dialog-content';
        content.className = 'bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl transform scale-95 transition-transform duration-300 text-center mx-4';
        
        overlay.appendChild(content);
        document.body.appendChild(overlay);
    }
    return overlay;
}

function closeDialog() {
    const overlay = document.getElementById('zt-dialog-overlay');
    if (overlay) {
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        document.getElementById('zt-dialog-content').classList.remove('scale-100');
        document.getElementById('zt-dialog-content').classList.add('scale-95');
    }
}

function openDialog() {
    const overlay = createDialogOverlay();
    // Force reflow
    void overlay.offsetWidth;
    overlay.classList.remove('opacity-0', 'pointer-events-none');
    overlay.classList.add('opacity-100', 'pointer-events-auto');
    document.getElementById('zt-dialog-content').classList.remove('scale-95');
    document.getElementById('zt-dialog-content').classList.add('scale-100');
}

/**
 * Thay thế cho alert()
 * @param {string} title Tiêu đề
 * @param {string} message Nội dung
 * @param {string} type 'info' | 'success' | 'error' | 'warning'
 */
function showDialog(title, message, type = 'info') {
    return new Promise((resolve) => {
        createDialogOverlay();
        const content = document.getElementById('zt-dialog-content');
        
        let iconHtml = '';
        let btnColor = 'bg-primary';
        
        if (type === 'success') {
            btnColor = 'bg-success';
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-success-light text-success mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>`;
        } else if (type === 'error') {
            btnColor = 'bg-danger';
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-danger-light text-danger mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div>`;
        } else if (type === 'warning') {
            btnColor = 'bg-warning';
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-warning-light text-warning mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>`;
        } else {
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary-light text-primary mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>`;
        }

        content.innerHTML = `
            ${iconHtml}
            <h3 class="text-lg leading-6 font-bold text-dark mb-2">${title}</h3>
            <p class="text-sm text-muted mb-6 px-2">${message}</p>
            <button id="zt-dialog-ok" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-3 ${btnColor} text-base font-bold text-white focus:outline-none sm:text-sm transition-all hover:opacity-90">OK</button>
        `;

        document.getElementById('zt-dialog-ok').onclick = () => {
            closeDialog();
            setTimeout(resolve, 300); // Đợi animation đóng
        };

        openDialog();
    });
}

/**
 * Thay thế cho confirm()
 * @param {string} title Tiêu đề
 * @param {string} message Nội dung
 * @param {string} confirmText Chữ trên nút Xác nhận
 * @param {boolean} isDanger Nút xác nhận màu Đỏ hay Xanh
 */
function showConfirm(title, message, confirmText = 'Xác nhận', isDanger = false) {
    return new Promise((resolve) => {
        createDialogOverlay();
        const content = document.getElementById('zt-dialog-content');
        
        const confirmBtnClass = isDanger ? 'bg-danger text-white hover:bg-red-600' : 'bg-primary text-white hover:bg-primary-hover';
        const iconHtml = isDanger 
            ? `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-danger-light text-danger mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>`
            : `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary-light text-primary mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>`;

        content.innerHTML = `
            ${iconHtml}
            <h3 class="text-lg leading-6 font-bold text-dark mb-2">${title}</h3>
            <p class="text-sm text-muted mb-6 px-2">${message}</p>
            <div class="flex flex-col gap-3">
                <button id="zt-confirm-yes" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-3 ${confirmBtnClass} text-base font-bold focus:outline-none sm:text-sm transition-all hover:opacity-90">${confirmText}</button>
                <button id="zt-confirm-no" class="w-full inline-flex justify-center rounded-2xl border-2 border-gray-100 px-4 py-3 bg-white text-muted font-bold hover:bg-gray-50 focus:outline-none sm:text-sm transition-all">Hủy bỏ</button>
            </div>
        `;

        document.getElementById('zt-confirm-yes').onclick = () => {
            closeDialog();
            setTimeout(() => resolve(true), 300);
        };

        document.getElementById('zt-confirm-no').onclick = () => {
            closeDialog();
            setTimeout(() => resolve(false), 300);
        };

        openDialog();
    });
}
