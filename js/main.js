console.log("🚀 main.js loaded thành công!");

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

document.addEventListener("DOMContentLoaded", () => {
    loadPhongBan();
    loadNhanVien();
    loadChamCong();
    loadLuong();


    // Gửi form tính lương
    document.getElementById('btnTinhLuong').addEventListener('click', async () => {
        console.log("🟡 Button clicked!");
    const form = document.getElementById('formTinhLuong');
    const formData = new FormData(form);

    const thang = formData.get("Thang"); // 👈 lấy tháng người chọn
    const nam = formData.get("Nam");
    
    try {
        console.log("🔹 Gửi request tới tinh_luong.php...");
        const response = await fetch('api/tinh_luong.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        // Nếu tính lương thành công thì load lại danh sách lương
        if (result.status === 'success') {
            console.log(`✅ Load bảng lương tháng ${thang}/${nam}`);
            await loadLuong(thang);
        }

    } catch (err) {
        console.error('❌ Lỗi khi tính lương:', err);
        alert('⚠️ Lỗi kết nối đến server!');
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
    const tbody = document.getElementById("tableNhanVien");
    tbody.innerHTML = "";
    data.forEach(nv => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${nv.MaNV}</td>
            <td>${nv.HoTen}</td>
            <td>${nv.TenPhongBan || "-"}</td>
            <td>${Number(nv.LuongCB).toLocaleString()} ₫</td>
            <td>${nv.SDT || ""}</td>
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
        console.error("❌ Lỗi load chấm công:", error);
    }
}

async function loadLuong(thang) {
    try {
        const res = await fetch(`api/get_luong.php?thang=${thang}`);
        const data = await res.json();
        const tbody = document.getElementById("tableLuong");
        tbody.innerHTML = "";

        let tongLuongCB = 0, tongTheoGio = 0, tongTangCa = 0, tongThuong = 0, tongPhuCap = 0, tongKhauTru = 0, tongTongLuong = 0;

        data.forEach((l, index) => {
            tongLuongCB += Number(l.LuongCB);
            tongTheoGio += Number(l.TheoGio);
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
                <td>${Number(l.TheoGio).toLocaleString()} ₫</td>
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
        document.getElementById("footerTheoGio").textContent = tongTheoGio.toLocaleString() + "đ";
        document.getElementById("footerTangCa").textContent = tongTangCa.toLocaleString() + "đ";
        document.getElementById("footerThuong").textContent = tongThuong.toLocaleString() + "đ";
        document.getElementById("footerPhuCap").textContent = tongPhuCap.toLocaleString() + "đ";
        document.getElementById("footerKhauTru").textContent = tongKhauTru.toLocaleString() + "đ";
        document.getElementById("footerTotal").textContent = tongTongLuong.toLocaleString() + "đ";

        document.getElementById("sumLuongCB").textContent = tongLuongCB.toLocaleString() + "đ";
        document.getElementById("sumTheoGio").textContent = tongTheoGio.toLocaleString() + "đ";
        document.getElementById("sumTangCa").textContent = tongTangCa.toLocaleString() + "đ";
        document.getElementById("sumThuong").textContent = tongThuong.toLocaleString() + "đ";
        document.getElementById("sumTotal").textContent = tongTongLuong.toLocaleString() + "đ";

    } catch (error) {
        console.error("❌ Lỗi load lương:", error);
    }
}
