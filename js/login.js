  // Toggle hiển thị mật khẩu
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = '<i class="far fa-eye"></i>';
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = '<i class="far fa-eye-slash"></i>';
            }
        }

        // Hiển thị thông báo
        function showAlert(message, type = 'danger') {
            const alert = document.getElementById('alert');
            alert.className = `alert alert-${type} show`;
            alert.textContent = message;
            
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        // Hiển thị lỗi cho từng trường
        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + 'Error');
            
            input.classList.add('error');
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }

        // Xóa lỗi
        function clearError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + 'Error');
            
            input.classList.remove('error');
            errorDiv.classList.remove('show');
        }

        // Xử lý submit form
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear errors
            clearError('username');
            clearError('password');
            
            // Lấy dữ liệu
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            // const rememberMe = document.getElementById('rememberMe').checked;
            
            // Validate
            let hasError = false;
            
            if (!username) {
                showError('username', 'Vui lòng nhập email hoặc tên đăng nhập');
                hasError = true;
            }
            
            if (!password) {
                showError('password', 'Vui lòng nhập mật khẩu');
                hasError = true;
            } else if (password.length < 6) {
                showError('password', 'Mật khẩu phải có ít nhất 6 ký tự');
                hasError = true;
            }
            
            if (hasError) return;
            
            // Disable button
            const btnLogin = document.getElementById('btnLogin');
            btnLogin.disabled = true;
            btnLogin.textContent = 'Đang xử lý...';
            
            try {
                // Gửi request
                const response = await fetch('../api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password,
                        // rememberMe: rememberMe
                    })
                });
                
                const data = await response.json();
                
                                if (data.success) {
                    // ✅ Hiển thị thông báo thành công
                    showAlert('Đăng nhập thành công! Đang chuyển hướng...', 'success');

                    // Thêm hiệu ứng nhỏ (ví dụ fade out body hoặc loading)
                    document.body.style.opacity = '0.8';
                    document.body.style.transition = 'opacity 0.6s ease';

                    // ⏳ Chờ 1.5 giây rồi chuyển trang
                    setTimeout(() => {
                        window.location.href = '../index.php'; 
                    }, 1500);

                } 
                else {
                    // ❌ Sai tài khoản hoặc mật khẩu
                    showAlert(data.message || 'Tên đăng nhập hoặc mật khẩu không chính xác!', 'danger');
                }
            } catch (error) {
                console.error('Lỗi khi đăng nhập:', error);
                showAlert('Không thể kết nối đến máy chủ. Vui lòng thử lại sau!', 'danger');
            } finally {
                // 🔁 Kích hoạt lại nút đăng nhập
                const btnLogin = document.getElementById('btnLogin');
                btnLogin.disabled = false;
                btnLogin.textContent = 'Đăng nhập';
            }
        });