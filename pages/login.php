<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - Hệ thống Quản lý Lương</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="../assets/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1>Hệ thống Quản lý Lương</h1>
            <p>Truy cập vào bảng điều khiển quản trị để quản lý nhân viên, chấm công và tính lương.</p>
            
            <div class="feature">
                <div class="feature-icon">🔐</div>
                <div>
                    <h3 style="margin-bottom: 5px;">Bảo mật cao</h3>
                    <p style="font-size: 14px; opacity: 0.8;">Chỉ tài khoản admin được cấp quyền mới có thể truy cập</p>
                </div>
            </div>
            
            <div class="feature">
                <div class="feature-icon">⚡</div>
                <div>
                    <h3 style="margin-bottom: 5px;">Quản lý tập trung</h3>
                    <p style="font-size: 14px; opacity: 0.8;">Kiểm soát toàn bộ thông tin nhân sự và bảng lương</p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Đăng nhập Admin</h2>
                <p>Nhập thông tin tài khoản quản trị viên</p>
            </div>

            <div id="alert" class="alert"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label>Email hoặc Tên đăng nhập <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            placeholder="admin@company.com"
                            autocomplete="username"
                        >
                    </div>
                    <div class="error-message" id="usernameError"></div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <span class="toggle-password" onclick="togglePassword()"><i class="far fa-eye-slash"></i></span>
                    </div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <!-- <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="rememberMe" name="rememberMe">
                        <label for="rememberMe">Lưu mật khẩu</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Quên mật khẩu?</a>
                </div> -->

                <button type="submit" class="btn-login" id="btnLogin">
                    Đăng nhập
                </button>
            </form>

            <p style="margin-top: 20px; text-align: center; color: #999; font-size: 13px;">
                 Hệ thống chỉ dành cho nhân viên có quyền quản trị
            </p>
        </div>
    </div>

</body>
<script src="../js/login.js"></script>
</html>
