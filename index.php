<?php
// Đặt ở đầu file PHP (trước mọi HTML output)
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: pages/login.php');
    exit;
}

// Lấy thông tin từ session
$adminName = $_SESSION['admin_name'] ?? 'Khách';
$adminRole = $_SESSION['admin_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống HR - Quản Lý Nhân Viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="./assets/style.css">
    <style>
    .header-actions {
        display: flex;
        gap: 2px;
        align-items: center;
        position: relative;
        display: inline-block;
    }

    .wrap-left .header-actions {
        padding-top: 5px;
    }

    .user-display {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #667eea 0%, #1e3c72 100%);
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .user-display >*{
        color: #fff;
    }
    .user-display:hover {
      opacity: 0.8;
    }

    .user-name {
        font-weight: 500;
        font-size: 14px;
        color: #fff;
    }

    .dropdown-arrow {
        font-size: 12px;
        transition: transform 0.3s;
        color: #fff;
    }

    .user-display:hover .dropdown-arrow {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 8px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: -webkit-fill-available;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }

    .dropdown-item:first-child {
        border-radius: 8px 8px 0 0;
    }

    .dropdown-item:last-child {
        border-bottom: none;
        border-radius: 0 0 8px 8px;
    }

    .dropdown-item:hover {
        background: #f5f5f5;
    }

    .dropdown-item.logout {
        color: #dc3545;
    }

    .dropdown-item.logout:hover {
        background: #fff5f5;
    }

    .role-badge {
        display: block;
        width: 100%;
        padding: 4px 8px;
        background: #1e3c72;
        color: #fff;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🏢 HR System</h2>
                <p>Hệ thống quản lý nhân sự</p>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Tổng Quan</div>
                <div class="menu-item active" data-section="dashboard">
                    <span class="menu-icon">📊</span>
                    <span>Dashboard</span>
                </div>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Nhân Sự</div>
                <div class="menu-item" data-section="nhanvien">
                    <span class="menu-icon">👥</span>
                    <span>Nhân Viên</span>
                </div>
                <div class="menu-item" data-section="phongban">
                    <span class="menu-icon">🏢</span>
                    <span>Phòng Ban</span>
                </div>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Chấm Công & Lương</div>
                <div class="menu-item" data-section="chamcong">
                    <span class="menu-icon">⏰</span>
                    <span>Chấm Công</span>
                </div>
                <div class="menu-item" data-section="luong">
                    <span class="menu-icon">💰</span>
                    <span>Bảng Lương</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="wrap-left">
                    <h1 id="pageTitle">Dashboard</h1>
                    <div class="header-actions">
                        ⏰<span id="currentDate"></span>
                    </div>
                </div>
                 <div class="header-actions">
                    <div class="user-display" onclick="toggleDropdown()">
                        <span><i class="fas fa-user"></i></span>
                        <span class="user-name"><?php echo htmlspecialchars($adminName); ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="dropdown-item">
                            <div class="role-badge">
                                <span ><?php echo htmlspecialchars($adminRole); ?></span>
                            </div>
                        </div>
                        <a href="api/logout.php" class="dropdown-item logout">
                            <span><i class="fas fa-sign-out-alt"></i></span>
                            <span>Đăng xuất</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <div class="alert alert-success" id="alertSuccess">
                    <span>✓</span>
                    <span id="alertSuccessText"></span>
                </div>
                <div class="alert alert-error" id="alertError">
                    <span>✗</span>
                    <span id="alertErrorText"></span>
                </div>

                <!-- Dashboard Section -->
                <div class="content-section active" id="dashboard">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                📊 Tổng Quan Hệ Thống
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Quý</label>
                                <select name="Quy" id="Quy">
                                    <option value="">-- Chọn quý --</option>
                                    <option value="1">Quý 1</option>
                                    <option value="2">Quý 2</option>
                                    <option value="3">Quý 3</option>
                                    <option value="4">Quý 4</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Năm</label>
                                <input type="number" name="Nam" id="Nam" value="2025" min="2020" max="2030" required>
                            </div>
                        </div>
                        <canvas id="chartLuong" height="120"></canvas>
                    </div>
                </div>

                <!-- Nhân Viên Section -->
                <div class="content-section" id="nhanvien">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">➕ Thêm Nhân Viên Mới</div>
                        </div>
                        <form id="formNhanVien">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Mã Nhân Viên <span class="required">*</span></label>
                                    <input type="text" name="MaNV" placeholder="VD: NV001" required>
                                </div>
                                <div class="form-group">
                                    <label>Họ và Tên <span class="required">*</span></label>
                                    <input type="text" name="TenNV" placeholder="Nguyễn Văn A" required>
                                </div>
                                <div class="form-group">
                                    <label>Phòng Ban</label>
                                    <select name="MaPB" id="selectPhongBan">
                                        <option value="">-- Chọn phòng ban --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lương Cơ Bản (VNĐ)</label>
                                    <input type="number" name="LuongCoBan" placeholder="8000000" value="0">
                                </div>
                                <div class="form-group">
                                    <label>Ngày Sinh</label>
                                    <input type="date" name="NgaySinh">
                                </div>
                                <div class="form-group">
                                    <label>Ngày Vào Làm</label>
                                    <input type="date" name="NgayVaoLam">
                                </div>
                                <div class="form-group">
                                    <label>Số Điện Thoại</label>
                                    <input type="tel" name="SoDienThoai" placeholder="0901234567">
                                </div>
                                <div class="form-group">
                                    <label>Địa Chỉ</label>
                                    <input type="text" name="DiaChi" placeholder="Quận 1, TP.HCM">
                                </div>
                            </div>
                            <button type="button" id="btnThemNhanVien" class="btn btn-primary">
                                <span>➕</span> Thêm Nhân Viên
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">👥 Danh Sách Nhân Viên</div>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ Tên</th>
                                        <th>Phòng Ban</th>
                                        <th>Lương CB</th>
                                        <th>SĐT</th>
                                        <th>Ngày Sinh</th>
                                        <th>Ngày Vào Làm</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="tableNhanVien"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Phòng Ban Section -->
                <div class="content-section" id="phongban">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">➕ Thêm Phòng Ban</div>
                        </div>
                        <form id="formPhongBan">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Mã Phòng Ban <span class="required">*</span></label>
                                    <input type="text" name="MaPB" placeholder="VD: PB01" required>
                                </div>
                                <div class="form-group">
                                    <label>Tên Phòng Ban <span class="required">*</span></label>
                                    <input type="text" name="TenPB" placeholder="Phòng Kỹ Thuật" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Mô Tả</label>
                                <textarea name="MoTa" placeholder="Mô tả chi tiết về phòng ban..." rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span>➕</span> Thêm Phòng Ban
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">🏢 Danh Sách Phòng Ban</div>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã PB</th>
                                        <th>Tên Phòng Ban</th>
                                        <th>Mô Tả</th>
                                        <th>Ngày Tạo</th>
                                    </tr>
                                </thead>
                                <tbody id="tablePhongBan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Chấm Công Section - NÂNG CẤP -->
                <div class="content-section" id="chamcong">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">⏰ Nhập Chấm Công Chi Tiết</div>
                        </div>
                        <form id="formChamCong">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Nhân Viên <span class="required">*</span></label>
                                    <select name="MaNV" id="selectNhanVien" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ngày <span class="required">*</span></label>
                                    <input type="date" name="Ngay" required>
                                </div>
                                <div class="form-group">
                                    <label>Giờ Vào <span class="required">*</span></label>
                                    <input type="time" name="GioVao" id="gioVao" value="08:00">
                                </div>
                                <div class="form-group">
                                    <label>Giờ Ra <span class="required">*</span></label>
                                    <input type="time" name="GioRa" id="gioRa" value="17:00">
                                </div>
                                <div class="form-group">
                                    <label>Tổng Giờ Làm <span class="required">*</span></label>
                                    <input type="number" name="GioLam" id="gioLam" step="0.5" value="8" required
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Loại Công <span class="required">*</span></label>
                                    <select name="LoaiCong" id="loaiCong" required>
                                        <option value="Công thường">Công thường</option>
                                        <option value="Tăng ca">Tăng ca</option>
                                        <option value="Nghỉ phép">Nghỉ phép</option>
                                        <option value="Lễ">Lễ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Ghi Chú / Lý Do</label>
                                <textarea name="GhiChu" placeholder="Nhập ghi chú, lý do đi muộn, nghỉ phép..."
                                    rows="2"></textarea>
                            </div>

                            <button type="button" id="btnThemChamCong" class="btn btn-primary">
                                <span>💾</span> Lưu Chấm Công
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">📋 Lịch Sử Chấm Công</div>
                        </div>

                        <div class="filter-bar">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="number" id="filterThangCC" min="1" max="12" value="10">
                            </div>
                            <div class="form-group">
                                <label>Năm</label>
                                <input type="number" id="filterNamCC" value="2025">
                            </div>
                            <div class="form-group">
                                <label>Nhân viên</label>
                                <select id="filterNhanVienCC">
                                    <option value="">Tất cả</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Loại công</label>
                                <select id="filterLoaiCong">
                                    <option value="">Tất cả</option>
                                    <option value="Công Thường">Công Thường</option>
                                    <option value="Tăng ca">Tăng ca</option>
                                    <option value="Nghỉ phép">Nghỉ phép</option>
                                    <option value="Lễ">Lễ</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="filterChamCong()">
                                <span>🔍</span> Lọc
                            </button>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Mã NV</th>
                                        <th>Họ Tên</th>
                                        <th>Giờ Vào</th>
                                        <th>Giờ Ra</th>
                                        <th>Giờ Làm</th>
                                        <th>Loại Công</th>
                                        <th>Trạng Thái</th>
                                        <th>Ghi Chú</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="tableChamCong"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lương Section - NÂNG CẤP -->
                <div class="content-section" id="luong">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">💰 Tính Lương Hàng Tháng</div>
                        </div>
                        <form id="formTinhLuong">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Tháng <span class="required">*</span></label>
                                    <select name="Thang" required>
                                        <option value="">-- Chọn tháng --</option>
                                        <option value="1">Tháng 1</option>
                                        <option value="2">Tháng 2</option>
                                        <option value="3">Tháng 3</option>
                                        <option value="4">Tháng 4</option>
                                        <option value="5">Tháng 5</option>
                                        <option value="6">Tháng 6</option>
                                        <option value="7">Tháng 7</option>
                                        <option value="8">Tháng 8</option>
                                        <option value="9">Tháng 9</option>
                                        <option value="10" selected>Tháng 10</option>
                                        <option value="11">Tháng 11</option>
                                        <option value="12">Tháng 12</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Năm <span class="required">*</span></label>
                                    <input type="number" name="Nam" value="2025" min="2020" max="2030" required>
                                </div>
                            </div>
                            <button type="button" id="btnTinhLuong" class="btn btn-success">
                                <span>🧮</span> Tính Lương Toàn Bộ Nhân Viên
                            </button>
                        </form>
                    </div>

                    <!-- Form nhập lương thưởng -->
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-header">
                            <div class="card-title">💎 Nhập Thưởng & Phụ Cấp</div>
                        </div>
                        <form id="formThuongPhuCap">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Nhân Viên <span class="required">*</span></label>
                                    <select name="MaNV" id="selectNhanVienThuong" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tháng <span class="required">*</span></label>
                                    <select name="Thang" id="ThangThuong" required>
                                        <option value="">-- Chọn tháng --</option>
                                        <option value="1">Tháng 1</option>
                                        <option value="2">Tháng 2</option>
                                        <option value="3">Tháng 3</option>
                                        <option value="4">Tháng 4</option>
                                        <option value="5">Tháng 5</option>
                                        <option value="6">Tháng 6</option>
                                        <option value="7">Tháng 7</option>
                                        <option value="8">Tháng 8</option>
                                        <option value="9">Tháng 9</option>
                                        <option value="10" selected>Tháng 10</option>
                                        <option value="11">Tháng 11</option>
                                        <option value="12">Tháng 12</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Thưởng (VNĐ)</label>
                                    <input type="number" name="Thuong" value="0" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Phụ Cấp (VNĐ)</label>
                                    <input type="number" name="PhuCap" value="0" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Khấu Trừ (VNĐ)</label>
                                    <input type="number" name="KhauTru" value="0" min="0">
                                </div>
                            </div>
                            <button type="button" id="btnCapNhatThuong" class="btn btn-primary">
                                <span>💾</span> Cập Nhật Thưởng / Phụ Cấp
                            </button>
                        </form>
                    </div>

                    <!-- BẢNG LƯƠNG CHI TIẾT -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">📊 Bảng Lương Chi Tiết</div>
                            <div>
                                <label>Bảo mật lương</label>
                                <input type="checkbox" id="checkboxBaoMatLuong">
                            </div>
                        </div>

                        <div class="filter-bar">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="number" id="filterThangLuong" min="1" max="12" value="10">
                            </div>
                            <div class="form-group">
                                <label>Năm</label>
                                <input type="number" id="filterNamLuong" value="2025">
                            </div>
                            <div class="form-group">
                                <label>Phòng ban</label>
                                <select name="MaPB" id="filterPhongBanLuong">
                                    <option value="">Tất cả phòng ban</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="handleLoadLuong()">
                                <span>🔍</span> Lọc
                            </button>
                        </div>

                        <!-- Tổng hợp lương -->
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div>
                                <div style="font-size: 12px; color: #6c757d;">Tổng Lương CB</div>
                                <div style="font-size: 20px; font-weight: bold; color: #1e3c72;" id="sumLuongCB">0đ
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #6c757d;">Tổng Tăng Ca</div>
                                <div style="font-size: 20px; font-weight: bold; color: #ffc107;" id="sumTangCa">0đ</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #6c757d;">Tổng Thưởng</div>
                                <div style="font-size: 20px; font-weight: bold; color: #328E6E;" id="sumThuong">0đ</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #6c757d;">Tổng Khấu trừ</div>
                                <div style="font-size: 20px; font-weight: bold; color: #17a2b8;" id="sumKhauTru">0đ
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #6c757d;">TỔNG CHI TRẢ</div>
                                <div style="font-size: 24px; font-weight: bold; color: #dc3545;" id="sumTotal">0đ</div>
                            </div>
                        </div>

                        <div class="table-container">
                            <table id="tableSalary">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã NV</th>
                                        <th>Họ Tên</th>
                                        <th>Phòng Ban</th>
                                        <th>Lương CB</th>
                                        <th>Tổng số giờ</th>
                                        <th>Tăng Ca</th>
                                        <th>Thưởng</th>
                                        <th>Phụ Cấp</th>
                                        <th>Khấu Trừ</th>
                                        <th>Tổng Lương</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="tableLuong"></tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa; font-weight: bold;">
                                        <td colspan="4">TỔNG CỘNG</td>
                                        <td id="footerLuongCB">0đ</td>
                                        <td id="footerTheoGio">0</td>
                                        <td id="footerTangCa">0đ</td>
                                        <td id="footerThuong">0đ</td>
                                        <td id="footerPhuCap">0đ</td>
                                        <td id="footerKhauTru">0đ</td>
                                        <td id="footerTotal" style="color: #dc3545; font-size: 16px;">0đ</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./js/main.js?v=2"></script>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
     }

    // Đóng dropdown khi click bên ngoài
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.header-actions')) {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.remove('show');
        }
    });
    // Đóng dropdown khi click bên ngoài
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.header-actions')) {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.remove('show');
        }
    });
</script>
</html>
