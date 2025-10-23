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
    const selectForm = document.getElementById("selectNhanVien");
    const selectFilter = document.getElementById("filterNhanVienCC");
    
    if (selectForm) selectForm.innerHTML = '<option value="">-- Chọn nhân viên --</option>';
    if (selectFilter) selectFilter.innerHTML = '<option value="">Tất cả</option>';

     // Duyệt danh sách nhân viên
        data.nhanvien.forEach(nv => {
            // Option cho form
            if (selectForm) {
                const opt1 = document.createElement("option");
                opt1.value = nv.MaNV;
                opt1.textContent = nv.HoTen;
                selectForm.appendChild(opt1);
            }

            // Option cho filter
            if (selectFilter) {
                const opt2 = document.createElement("option");
                opt2.value = nv.MaNV;
                opt2.textContent = nv.HoTen;
                selectFilter.appendChild(opt2);
            }
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
    filterChamCong();

    // Gửi form thêm/chỉnh sửa nhân viên
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

    // Gửi form chấm công
    document.getElementById("btnThemChamCong").addEventListener("click", async () => {
    const form = document.getElementById("formChamCong");
    const formData = new FormData(form);
    const btn = document.getElementById("btnThemChamCong");

    const isEdit = btn.dataset.editMode === "true";
    const apiUrl = isEdit ? "api/edit_chamcong.php" : "api/add_chamcong.php";

    try {
        const res = await fetch(apiUrl, {
            method: "POST",
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            alert("✅ " + result.message);
            form.reset();
            document.getElementById("gioVao").value = "08:00";
            document.getElementById("gioRa").value = "17:00";
            document.getElementById("gioLam").value = "8";

            // Reset nút lại về chế độ thêm
            btn.innerHTML = `<span>💾</span> Lưu Chấm Công`;
            delete btn.dataset.editMode;
        } else {
            alert("❌ " + result.message);
        }

        loadChamCong();
    } catch (error) {
        alert("🚨 Lỗi khi gửi yêu cầu: " + error.message);
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
                <td>
                    <button class="btn-edit" onclick="editChamCong('${cc.MaNV}', '${cc.Ngay}')">✏️</button>
                    <button class="btn-delete" onclick="deleteChamCong('${cc.MaNV}', '${cc.Ngay}')">🗑️</button>
                </td>
                
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("Lỗi load chấm công:", error);
    }
}

async function editChamCong(maNV, ngay) {
    try {
        const res = await fetch(`api/get_chamcong.php?MaNV=${maNV}&Ngay=${ngay}`);
        const data = await res.json();

        // tìm dòng chấm công cần sửa
        const record = data.find(cc => cc.MaNV === maNV && cc.Ngay === ngay);
        if (!record) return alert("Không tìm thấy dữ liệu chấm công!");

        // Gán dữ liệu vào form
         // Nếu chưa load danh sách nhân viên thì chờ load xong mới gán
        const select = document.getElementById("selectNhanVien");

        // Đợi một chút để chắc chắn select có đầy đủ option (nếu load async)
        if (select.options.length <= 1) {
            await loadNhanVien(); // gọi lại API load nhân viên
        }

        // Gán giá trị cho form
        select.value = record.MaNV;
        document.querySelector('[name="Ngay"]').value = record.Ngay;
        document.querySelector('[name="GioVao"]').value = record.GioVao;
        document.querySelector('[name="GioRa"]').value = record.GioRa;
        document.querySelector('[name="GioLam"]').value = record.GioLam;
        document.querySelector('[name="LoaiCong"]').value = record.LoaiCong;
        document.querySelector('[name="GhiChu"]').value = record.GhiChu;

        // đổi text nút
        const btn = document.getElementById("btnThemChamCong");
        btn.innerHTML = `<span>💾</span> Cập Nhật Chấm Công`;
        btn.dataset.editMode = "true"; // đánh dấu đang ở chế độ edit
    } catch (err) {
        alert("Lỗi khi tải dữ liệu chấm công: " + err.message);
    }
}

async function deleteChamCong(maNV, ngay) {
    if (!confirm("⚠️ Bạn có chắc muốn xoá chấm công này không?")) return;

    try {
        const res = await fetch("api/delete_chamcong.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `MaNV=${encodeURIComponent(maNV)}&Ngay=${encodeURIComponent(ngay)}`
        });

        const result = await res.json();

        if (result.success) {
            alert("✅ " + result.message);
            loadChamCong(); // reload lại danh sách
        } else {
            alert("❌ " + result.message);
        }
    } catch (error) {
        alert("🚨 Lỗi khi xoá: " + error.message);
    }
}

async function filterChamCong() {
    try {
        const thang = document.getElementById("filterThangCC")?.value || "";
        const nam = document.getElementById("filterNamCC")?.value || "";
        const maNV = document.getElementById("filterNhanVienCC")?.value || "";
        const loaiCong = document.getElementById("filterLoaiCong")?.value || "";

        const params = new URLSearchParams({
            Thang: thang,
            Nam: nam,
            MaNV: maNV,
            LoaiCong: loaiCong
        });

        const res = await fetch(`api/get_chamcong.php?${params.toString()}`);
        const data = await res.json();

        const tbody = document.getElementById("tableChamCong");
        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" style="text-align:center;">Không có dữ liệu phù hợp 🫠</td></tr>`;
            return;
        }

        data.forEach(cc => {
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
                <td>
                    <button class="btn-edit" onclick="editChamCong('${cc.MaNV}', '${cc.Ngay}')">✏️</button>
                    <button class="btn-delete" onclick="deleteChamCong('${cc.MaNV}', '${cc.Ngay}')">🗑️</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("⚠️ Lỗi khi lọc chấm công:", error);
        alert("🚨 Có lỗi khi lọc dữ liệu!");
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


const gioVaoInput = document.getElementById("gioVao");
const gioRaInput = document.getElementById("gioRa");
const gioLamInput = document.getElementById("gioLam");

  function tinhGioLam() {
    const gioVao = gioVaoInput.value;
    const gioRa = gioRaInput.value;

    if (!gioVao || !gioRa) return;

    let [vaoH, vaoM] = gioVao.split(":").map(Number);
    let [raH, raM] = gioRa.split(":").map(Number);

    const vaoPhut = vaoH * 60 + vaoM;
    const raPhut = raH * 60 + raM;

    let minutes = 0;

    if (vaoPhut >= 17 * 60) {
        // Làm tăng ca (bắt đầu sau 5h chiều) => tính giờ bình thường
        if (raPhut < vaoPhut) {
        // qua ngày hôm sau
        minutes = (24 * 60 - vaoPhut) + raPhut;
        } else {
        minutes = raPhut - vaoPhut;
        }
    } else {
    // 🔹 Làm giờ hành chính
    // Giới hạn giờ vào sớm nhất là 08:00
    if (vaoH < 8) {
      vaoH = 8;
      vaoM = 0;
    }

    // Giới hạn giờ ra tối đa là 17:00
    if (raH > 17 || (raH === 17 && raM > 0)) {
      raH = 17;
      raM = 0;
    }
  }

    let start = vaoH * 60 + vaoM;
    let end = raH * 60 + raM;

    // Nếu giờ ra nhỏ hơn giờ vào -> qua ngày hôm sau
    if (end < start) end += 24 * 60;

    minutes = end - start;

     // Trừ 1 tiếng nghỉ trưa nếu làm qua khung giờ 12h–13h
    if (start < 12 * 60 && end > 13 * 60) {
        minutes -= 60;
    }

    const hours = (minutes / 60).toFixed(1); // làm tròn 1 chữ số thập phân
    gioLamInput.value = hours;
  }

  gioVaoInput.addEventListener("change", tinhGioLam);
  gioRaInput.addEventListener("change", tinhGioLam);