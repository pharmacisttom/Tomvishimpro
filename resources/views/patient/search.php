<?php
$title = "ค้นหาข้อมูลผู้ป่วย - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-user-injured text-primary me-2"></i>Patient Search & Clinical Record</h3>
    <p class="text-muted">ค้นหาประวัติผู้ป่วยจาก HIMPro HIS Gateway (Mock / Sandbox)</p>
</div>

<div class="card border-0 shadow-sm p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-9">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="พิมพ์ HN (เช่น 67000001, 67000002) หรือชื่อผู้ป่วย..." value="67000001">
            </div>
        </div>
        <div class="col-md-3">
            <button id="btnSearch" class="btn btn-primary btn-lg w-100 fw-bold">
                <i class="fa-solid fa-magnifying-glass me-1"></i> ค้นหาข้อมูล
            </button>
        </div>
    </div>
</div>

<div id="patientCard" class="card border-0 shadow-sm p-4 d-none">
    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
        <div>
            <h4 class="fw-bold text-primary mb-1" id="pName">สมชาย ใจดี</h4>
            <span class="badge bg-secondary me-2">HN: <span id="pHn">67000001</span></span>
            <span class="badge bg-info me-2">CID: <span id="pCid">11099900001</span></span>
            <span class="badge bg-dark">เพศ: <span id="pSex">ชาย</span></span>
        </div>
        <div id="allergyBadge">
            <span class="badge bg-success fs-6"><i class="fa-solid fa-shield-check me-1"></i> ไม่มีประวัติแพ้ยา</span>
        </div>
    </div>

    <!-- Tabs for Patient Info -->
    <ul class="nav nav-tabs border-bottom mb-3" id="patientTabs">
        <li class="nav-item">
            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-visits"><i class="fa-solid fa-calendar-alt me-1"></i> ประวัติการมารับบริการ</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-meds"><i class="fa-solid fa-pills me-1"></i> รายการยาที่ได้รับ</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-labs"><i class="fa-solid fa-vial me-1"></i> ผลตรวจทางห้องปฏิบัติการ</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-visits">
            <table class="table table-hover" id="tableVisits">
                <thead class="table-light">
                    <tr>
                        <th>VN</th>
                        <th>วันที่รับบริการ</th>
                        <th>แผนก</th>
                        <th>แพทย์ผู้ตรวจ</th>
                        <th>อาการสำคัญ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="tab-meds">
            <table class="table table-hover" id="tableMeds">
                <thead class="table-light">
                    <tr>
                        <th>รหัสยา</th>
                        <th>ชื่อยา</th>
                        <th>วิธีใช้</th>
                        <th>ทางสั่ง</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="tab-labs">
            <table class="table table-hover" id="tableLabs">
                <thead class="table-light">
                    <tr>
                        <th>รหัส Lab</th>
                        <th>รายการ</th>
                        <th>ผลตรวจ</th>
                        <th>หน่วย</th>
                        <th>ค่าปกติ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("btnSearch").addEventListener("click", performSearch);

        function performSearch() {
            const query = document.getElementById("searchInput").value;
            fetch(`/tomvishimpro/public/api/v1/patients/${query}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        const p = res.data;
                        document.getElementById("patientCard").classList.remove("d-none");
                        document.getElementById("pName").innerText = p.full_name;
                        document.getElementById("pHn").innerText = p.hn;
                        document.getElementById("pCid").innerText = p.cid;
                        document.getElementById("pSex").innerText = p.sex === 'M' ? 'ชาย' : 'หญิง';

                        if (p.allergies && p.allergies.length > 0) {
                            document.getElementById("allergyBadge").innerHTML = `<span class="badge bg-danger fs-6"><i class="fa-solid fa-triangle-exclamation me-1"></i> แพ้ยา: ${p.allergies[0].drug_name} (${p.allergies[0].severity})</span>`;
                        } else {
                            document.getElementById("allergyBadge").innerHTML = `<span class="badge bg-success fs-6"><i class="fa-solid fa-shield-check me-1"></i> ไม่มีประวัติแพ้ยา</span>`;
                        }

                        // Populate Visits
                        let vRows = '';
                        (p.recent_visits || []).forEach(v => {
                            vRows += `<tr><td>${v.vn}</td><td>${v.visit_date}</td><td>${v.department}</td><td>${v.physician}</td><td>${v.chief_complaint}</td></tr>`;
                        });
                        document.querySelector("#tableVisits tbody").innerHTML = vRows;

                        // Fetch Meds
                        fetch(`/tomvishimpro/public/api/v1/medrec/bpmh/${p.hn}`)
                            .then(r => r.json())
                            .then(mRes => {
                                let mRows = '';
                                (mRes.data || []).forEach(m => {
                                    mRows += `<tr><td>${m.drug_code}</td><td><strong>${m.drug_name}</strong></td><td>${m.dose} ${m.frequency}</td><td>${m.route}</td></tr>`;
                                });
                                document.querySelector("#tableMeds tbody").innerHTML = mRows;
                            });

                    } else {
                        Swal.fire('ไม่พบข้อมูล', 'ไม่พบข้อมูลผู้ป่วยตาม HN ที่ระบุ', 'warning');
                    }
                });
        }

        // Auto trigger search on initial load
        performSearch();
    });
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
