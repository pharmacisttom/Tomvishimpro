<?php
$title = "Deployment Manager - TomvisHIMPro";
ob_start();
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold"><i class="fa-solid fa-rocket text-primary me-2"></i>Zero-Reconfiguration Deployment Manager</h3>
        <p class="text-muted">บริหารจัดการ Release Deployment และ Rollback สำหรับเครื่องพัฒนาและ Hospital Server</p>
    </div>
    <div>
        <button class="btn btn-primary fw-bold" onclick="runWebDeploy()">
            <i class="fa-solid fa-paper-plane me-1"></i> Trigger Release Deploy
        </button>
        <button class="btn btn-outline-danger fw-bold ms-2" onclick="runWebRollback()">
            <i class="fa-solid fa-rotate-left me-1"></i> Rollback Stable
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-clock-history me-2 text-primary"></i>Deployment History & Preflight Log</h5>
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Release Tag</th>
                <th>Target Host</th>
                <th>Environment</th>
                <th>Preflight Status</th>
                <th>Deploy Result</th>
                <th>Deployed At</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>v3.0.0-rc1</code></td>
                <td>192.168.111.240</td>
                <td><span class="badge bg-primary">Local Dev</span></td>
                <td><span class="badge bg-success">PASSED</span></td>
                <td><span class="badge bg-success">SUCCESS</span></td>
                <td>2026-10-10 12:45:00</td>
            </tr>
            <tr>
                <td><code>v2.1.0</code></td>
                <td>192.168.111.240</td>
                <td><span class="badge bg-primary">Local Dev</span></td>
                <td><span class="badge bg-success">PASSED</span></td>
                <td><span class="badge bg-success">SUCCESS</span></td>
                <td>2026-10-10 12:30:00</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function runWebDeploy() {
        Swal.fire('Deploy Triggered', 'เริ่มกระบวนการ Preflight Verification และ Zero-Reconfiguration Deploy...', 'info');
    }
    function runWebRollback() {
        Swal.fire('Rollback Executed', 'ย้อนระบบกลับสู่ Release ก่อนหน้า โดยไม่แตะต้องไฟล์ .env', 'success');
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
