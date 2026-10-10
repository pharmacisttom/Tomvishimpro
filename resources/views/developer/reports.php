<?php
$title = "Report & SQL Builder - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-chart-line text-success me-2"></i>Smart Report & SQL Builder</h3>
    <p class="text-muted">สร้างรายงานวิเคราะห์ข้อมูลสุขภาพด้วย Approved Dataset Allowlist และระบบปีงบประมาณไทย</p>
</div>

<div class="card border-0 shadow-sm p-4 mb-4">
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">เลือกแม่แบบรายงาน (Report Template)</label>
            <select class="form-select" id="reportSelect">
                <option value="opd_visits">รายงานสรุปผู้ป่วย OPD รายเดือน</option>
                <option value="drug_allergy">รายงานผู้ป่วยแพ้ยาและ ADR</option>
                <option value="pharmacy_queue">รายงานประสิทธิภาพคิวห้องยา PDH</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">เลือกปีงบประมาณไทย</label>
            <select class="form-select">
                <option value="2569" selected>ปีงบประมาณ 2569</option>
                <option value="2568">ปีงบประมาณ 2568</option>
            </select>
        </div>
        <div class="col-md-5 d-flex align-items-end gap-2">
            <button class="btn btn-primary fw-bold" onclick="generateReport()"><i class="fa-solid fa-play me-1"></i> ประมวลผลรายงาน</button>
            <button class="btn btn-outline-success fw-bold" onclick="exportCsv()"><i class="fa-solid fa-file-csv me-1"></i> Export CSV (UTF-8)</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>เดือน</th>
                    <th>ผู้ป่วย OPD (คน)</th>
                    <th>ประวัติแพ้ยา (ราย)</th>
                    <th>ADR (ราย)</th>
                    <th>สถานะประมวลผล</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>ตุลาคม 2568</td><td>1,240</td><td>12</td><td>2</td><td><span class="badge bg-success">Verified</span></td></tr>
                <tr><td>พฤศจิกายน 2568</td><td>1,350</td><td>15</td><td>3</td><td><span class="badge bg-success">Verified</span></td></tr>
                <tr><td>ธันวาคม 2568</td><td>1,420</td><td>9</td><td>1</td><td><span class="badge bg-success">Verified</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function generateReport() {
        Swal.fire('ประมวลผลสำเร็จ', 'รายงานสรุปข้อมูลถูกสร้างเรียบร้อยแล้ว', 'success');
    }
    function exportCsv() {
        Swal.fire('Exporting', 'กำลังดาวน์โหลดไฟล์ CSV (UTF-8 BOM)...', 'info');
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
