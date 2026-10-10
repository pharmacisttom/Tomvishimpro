<?php
$title = "Medication Reconciliation - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-pills text-success me-2"></i>Medication Reconciliation Workspace</h3>
    <p class="text-muted">ระบบกระทบยอดรายการยาผู้ป่วยในและผู้ป่วยนอก (BPMH vs Admission Orders)</p>
</div>

<div class="card border-0 shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-check me-2 text-primary"></i>เลือกผู้ป่วยสำหรับ MedRec</h5>
        <span class="badge bg-success">Patient HN: 67000001</span>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>รายการยาเดิมของผู้ป่วย (BPMH)</th>
                <th>ขนาดยา & วิธีใช้</th>
                <th>คำสั่งแพทย์เมื่อ Admit</th>
                <th>การจัดการของเภสัชกร (Pharmacist Action)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Amoxicillin 500 mg cap</strong></td>
                <td>1 cap tid pc</td>
                <td><span class="badge bg-danger">ไม่ได้สั่งซื้อเพิ่ม</span></td>
                <td>
                    <select class="form-select form-select-sm">
                        <option value="hold" selected>HOLD (ชะลอการใช้)</option>
                        <option value="continue">CONTINUE (ใช้ยาเดิมต่อ)</option>
                        <option value="stop">STOP (ยกเลิกยา)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Warfarin 3 mg tab</strong></td>
                <td>1 tab od hs</td>
                <td>Warfarin 3 mg tab 1 tab od hs</td>
                <td>
                    <select class="form-select form-select-sm">
                        <option value="continue" selected>CONTINUE (ใช้ยาเดิมต่อ)</option>
                        <option value="adjust">ADJUST (ปรับขนาดยาตาม INR)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Metformin 500 mg tab</strong></td>
                <td>1 tab bid pc</td>
                <td>Metformin 500 mg tab 1 tab bid pc</td>
                <td>
                    <select class="form-select form-select-sm">
                        <option value="continue" selected>CONTINUE (ใช้ยาเดิมต่อ)</option>
                        <option value="hold">HOLD (ระวังในผู้ป่วย eGFR ต่ำ)</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> พิมพ์ใบทบทวนยา A5</button>
        <button class="btn btn-success fw-bold" onclick="saveMedRec()"><i class="fa-solid fa-check me-1"></i> ยืนยันการทบทวนโดยเภสัชกร</button>
    </div>
</div>

<script>
    function saveMedRec() {
        Swal.fire('บันทึกสำเร็จ', 'ยืนยันผลการทบทวนรายการยา (Medication Reconciliation) เรียบร้อยแล้ว', 'success');
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
