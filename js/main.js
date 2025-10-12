
// Hiển thị ngày hiện tại
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
});

// Menu Navigation
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        const section = this.getAttribute('data-section');
        
        document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById(section).classList.add('active');
        
        const titles = {
            dashboard: 'Dashboard',
            nhanvien: 'Quản Lý Nhân Viên',
            phongban: 'Quản Lý Phòng Ban',
            chamcong: 'Chấm Công & Điểm Danh',
            luong: 'Quản Lý Lương & Thưởng',
        };
        document.getElementById('pageTitle').textContent = titles[section];
    });
});
