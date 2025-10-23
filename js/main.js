console.log("🚀 main.js loaded thành công!");
let editingMaNV = null; // nếu null => đang thêm mới, có giá trị => đang sửa

// Hiển thị ngày hiện tại
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
});

async function loadSelectPhongBan() {
    const res = await fetch("api/get_phongban.php");
    const data = await res.json();

    const select = document.getElementById("selectPhongBan");
    select.innerHTML = '<option value="">-- Chọn phòng ban --</option>';

    data.forEach(pb => {
        const option = document.createElement("option");
        option.value = pb.MaPB;
        option.textContent = pb.TenPhongBan;
        select.appendChild(option); 
    });
}

async function loadSelectNhanVien() {
    const res = await fetch("api/get_nhanvien.php");
    const data = await res.json();

    const select = document.getElementById("selectNhanVien");
    select.innerHTML = '<option value="">-- Chọn nhân viên --</option>';

    data.forEach(nv => {
        const option = document.createElement("option");
        option.value = nv.MaNV;
        option.textContent = nv.HoTen;
        select.appendChild(option);
    });
}


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

document.addEventListener("DOMContentLoaded", () => {
    loadPhongBan();
    loadNhanVien();
    loadChamCong();
    loadLuong();
    loadSelectPhongBan();
    loadSelectNhanVien();

    document.getElementById("btnThemNhanVien").addEventListener("click", async (e) => {
        e.preventDefault();

        const form = document.getElementById("formNhanVien");
        const formData = new FormData(form);

        // Nếu đang chỉnh sửa thì thêm MaNV vào formData để backend biết
        if (editingMaNV) {
            formData.append("MaNV", editingMaNV);
        }

        const apiUrl = editingMaNV ? "api/edit_nhanvien.php" : "api/add_nhanvien.php";
        const actionText = editingMaNV ? "Cập nhật" : "Thêm";

        try {
            const response = await fetch(apiUrl, {
                method: "POST",
                body: formData
            });

            const result = await response.json();
            console.log(`Kết quả ${actionText}:`, result);

            if (result.success) {
                alert(`${actionText} nhân viên thành công!`);
                form.reset();
                editingMaNV = null;
                document.getElementById("btnThemNhanVien").innerHTML = "<span>➕</span> Thêm Nhân Viên";
                loadNhanVien();
            } else {
                alert(`❌ Lỗi ${actionText.toLowerCase()} nhân viên: ${result.error || "Không rõ lỗi"}`);
            }
        } catch (error) {
            console.error("Lỗi khi gửi dữ liệu:", error);
            alert("Không thể kết nối đến server!");
        }
    });


    // Gửi form tính lương
    document.getElementById('btnTinhLuong').addEventListener('click', async () => {
        const form = document.getElementById('formTinhLuong');
        const formData = new FormData(form);

        const thang = formData.get("Thang");

        try {
            const response = await fetch('api/tinh_luong.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            console.log(result);

            // Sau khi tính xong có thể load lại danh sách lương
            loadLuong(thang); 
        } catch (error) {
            console.error("Lỗi:", error);
            alert("Đã xảy ra lỗi khi tính lương!");
        }
    });
});

async function loadPhongBan() {
    const res = await fetch("api/get_phongban.php");
    const data = await res.json();
    const select = document.getElementById("tablePhongBan");
    data.forEach(pb => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${pb.MaPB}</td>
            <td>${pb.TenPhongBan}</td>
            <td>${pb.MoTa}</td>
            <td>${pb.NgayTao}</td>
            <td><button class="btn-edit" onclick="editPhongBan('${pb.MaPB}')">✏️</button></td>
        `;
        select.appendChild(tr);
    });
}

async function loadNhanVien() {
    const res = await fetch("api/get_nhanvien.php");
    const data = await res.json();
       const nhanviens = data.nhanvien;
    const tbody = document.getElementById("tableNhanVien");
    tbody.innerHTML = "";
    nhanviens.forEach(nv => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${nv.MaNV}</td>
            <td>${nv.HoTen}</td>
            <td>${nv.TenPhongBan || "-"}</td>
            <td>${Number(nv.LuongCB).toLocaleString()} ₫</td>
            <td>${nv.SDT || ""}</td>
            <td>${nv.NgaySinh || "-"}</td>
            <td>${nv.NgayVaoLam || "-"}</td>
            <td>${nv.TrangThai}</td>
            <td><button class="btn-edit" onclick="editNhanVien('${nv.MaNV}')">✏️</button></td>
        `;
        tbody.appendChild(tr);
    });
}

async function loadChamCong() {
    try {
        const res = await fetch("api/get_chamcong.php");
        const data = await res.json();
        const tbody = document.getElementById("tableChamCong");
        tbody.innerHTML = "";

        data.forEach((cc, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${cc.Ngay}</td>
                <td>${cc.MaNV}</td>
                <td>${cc.HoTen || "-"}</td>
                <td>${cc.GioVao || "-"}</td>
                <td>${cc.GioRa || "-"}</td>
                <td>${cc.GioLam || "0"}</td>
                <td>${cc.LoaiCong || "-"}</td>
                <td>${cc.TrangThai || "-"}</td>
                <td>${cc.GhiChu || ""}</td>
                <td><button class="btn-edit" onclick="editChamCong('${cc.MaNV}', '${cc.Ngay}')">✏️</button></td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("Lỗi load chấm công:", error);
    }
}

async function loadLuong(thang) {
    try {
        const res = await fetch(`api/get_luong.php?thang=${thang}`);
        const data = await res.json();
        const tbody = document.getElementById("tableLuong");
        tbody.innerHTML = "";

        let tongLuongCB = 0, tongGioLam = 0, tongTangCa = 0, tongThuong = 0, tongPhuCap = 0, tongKhauTru = 0, tongTongLuong = 0;

        data.forEach((l, index) => {
            tongLuongCB += Number(l.LuongCB);
            tongGioLam += Number(l.TongGioLam);
            tongTangCa += Number(l.TangCa);
            tongThuong += Number(l.Thuong);
            tongPhuCap += Number(l.PhuCap);
            tongKhauTru += Number(l.KhauTru);
            tongTongLuong += Number(l.TongLuong);

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${l.MaNV}</td>
                <td>${l.HoTen}</td>
                <td>${l.TenPhongBan || "-"}</td>
                <td>${Number(l.LuongCB).toLocaleString()} ₫</td>
                <td>${Number(l.TongGioLam).toLocaleString()}</td>
                <td>${Number(l.TangCa).toLocaleString()} ₫</td>
                <td>${Number(l.Thuong).toLocaleString()} ₫</td>
                <td>${Number(l.PhuCap).toLocaleString()} ₫</td>
                <td>${Number(l.KhauTru).toLocaleString()} ₫</td>
                <td style="font-weight:bold; color:#dc3545">${Number(l.TongLuong).toLocaleString()} ₫</td>
                <td><button class="btn-edit" onclick="editLuong('${l.MaNV}')">✏️</button></td>
            `;
            tbody.appendChild(tr);
        });

        // Footer tổng cộng
        document.getElementById("footerLuongCB").textContent = tongLuongCB.toLocaleString() + "đ";
        document.getElementById("footerTheoGio").textContent = tongGioLam.toLocaleString();
        document.getElementById("footerTangCa").textContent = tongTangCa.toLocaleString() + "đ";
        document.getElementById("footerThuong").textContent = tongThuong.toLocaleString() + "đ";
        document.getElementById("footerPhuCap").textContent = tongPhuCap.toLocaleString() + "đ";
        document.getElementById("footerKhauTru").textContent = tongKhauTru.toLocaleString() + "đ";
        document.getElementById("footerTotal").textContent = tongTongLuong.toLocaleString() + "đ";

        document.getElementById("sumLuongCB").textContent = tongLuongCB.toLocaleString() + "đ";
        document.getElementById("sumTangCa").textContent = tongTangCa.toLocaleString() + "đ";
        document.getElementById("sumThuong").textContent = tongThuong.toLocaleString() + "đ";
        document.getElementById("sumTotal").textContent = tongTongLuong.toLocaleString() + "đ";

    } catch (error) {
        console.error("Lỗi load lương:", error);
    }
}

async function editNhanVien(MaNV) {
    try {
        const response = await fetch(`api/get_nhanvien.php?MaNV=${MaNV}`);
        const data = await response.json();

        if (!data.success || !data.nhanvien) {
            alert("❌ Không tìm thấy dữ liệu nhân viên!");
            return;
        }

        const nv = data.nhanvien;
        console.log("🧩 Dữ liệu nhân viên:", nv);

        // Gán dữ liệu lên form
        document.querySelector("input[name='MaNV']").value = nv.MaNV;
        document.querySelector("input[name='TenNV']").value = nv.HoTen;
        document.querySelector("select[name='MaPB']").value = nv.MaPB || "";
        document.querySelector("input[name='LuongCoBan']").value = nv.LuongCB || 0;
        document.querySelector("input[name='NgaySinh']").value = nv.NgaySinh || "";
        document.querySelector("input[name='NgayVaoLam']").value = nv.NgayVaoLam || "";
        document.querySelector("input[name='SoDienThoai']").value = nv.SDT || "";
        document.querySelector("input[name='DiaChi']").value = nv.DiaChi || "";

        // Lưu trạng thái đang edit
        editingMaNV = nv.MaNV;

        // Đổi nút thành “Lưu chỉnh sửa”
        document.getElementById("btnThemNhanVien").innerHTML = "<span>💾</span> Lưu Chỉnh Sửa";
    } catch (err) {
        console.error("Lỗi khi lấy dữ liệu nhân viên:", err);
        alert("Không thể tải thông tin nhân viên để chỉnh sửa!");
    }
}
