<?php
$title = "Sandbox Studio - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-vial text-purple me-2"></i>TomvisHIMPro Healthcare Sandbox Studio</h3>
    <p class="text-muted">สภาพแวดล้อมจำลองข้อมูลผู้ป่วย Synthetic Data สำหรับการพัฒนาและทดสอบความปลอดภัย</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check me-2 text-primary"></i>Clinical Test Scenarios</h5>
            <div class="list-group">
                <button class="list-group-item list-group-item-action active fw-bold" onclick="loadScenario('no_allergy')">
                    <i class="fa-solid fa-check-circle me-2"></i> 1. Normal Patient (No Allergy)
                </button>
                <button class="list-group-item list-group-item-action" onclick="loadScenario('penicillin_allergy')">
                    <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i> 2. Severe Penicillin Allergy
                </button>
                <button class="list-group-item list-group-item-action" onclick="loadScenario('adr_history')">
                    <i class="fa-solid fa-user-ninja me-2 text-warning"></i> 3. ADR Stevens-Johnson Syndrome
                </button>
                <button class="list-group-item list-group-item-action" onclick="loadScenario('warfarin_therapy')">
                    <i class="fa-solid fa-pills me-2 text-info"></i> 4. High-Risk Warfarin Therapy
                </button>
                <button class="list-group-item list-group-item-action" onclick="loadScenario('ckd_stage4')">
                    <i class="fa-solid fa-notes-medical me-2 text-primary"></i> 5. Severe CKD (eGFR < 30)
                </button>
            </div>
            <div class="d-grid mt-4">
                <button class="btn btn-outline-danger" onclick="regenerateDataset()">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Reset & Seed Dataset
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-code me-2 text-success"></i>JSON Response & DTO Inspector</h5>
                <span class="badge bg-dark">Seed: <span id="currentSeed">42</span></span>
            </div>
            <pre class="bg-dark text-success p-3 rounded" style="max-height: 480px; overflow-y: auto;"><code id="jsonViewer">// Select a clinical scenario to view synthetic data output...</code></pre>
        </div>
    </div>
</div>

<script>
    function loadScenario(code) {
        fetch(`/tomvishimpro/public/api/v1/patients/67000001`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('jsonViewer').innerText = JSON.stringify(data, null, 4);
            });
    }

    function regenerateDataset() {
        Swal.fire({
            title: 'ยืนยันการรีเซ็ตข้อมูล?',
            text: "ระบบจะทำการสุ่มสร้างข้อมูลผู้ป่วย Synthetic Data ใหม่ 50 ราย",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยันสร้างใหม่',
            cancelButtonText: 'ยกเลิก'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch('/tomvishimpro/public/api/v1/sandbox/generate', { method: 'POST' })
                    .then(r => r.json())
                    .then(d => {
                        Swal.fire('สำเร็จ', 'สร้างข้อมูล Synthetic Dataset ใหม่เรียบร้อยแล้ว', 'success');
                        document.getElementById('jsonViewer').innerText = JSON.stringify(d.data, null, 4);
                    });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadScenario('no_allergy');
    });
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
