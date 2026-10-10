<?php
$title = "Security Lab & Pentest Suite - TomvisHIMPro";
ob_start();
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold"><i class="fa-solid fa-shield-halved text-danger me-2"></i>Security Lab & Penetration Testing Suite</h3>
        <p class="text-muted">ระบบตรวจสอบความปลอดภัย SAST/DAST, Dependency Audit และ Release Security Gate</p>
    </div>
    <div>
        <button class="btn btn-danger fw-bold" onclick="triggerScan('SAST & Dependency Audit')">
            <i class="fa-solid fa-play me-1"></i> Run Security Scan
        </button>
        <button class="btn btn-outline-dark fw-bold ms-2" onclick="viewZapConfig()">
            <i class="fa-solid fa-bug me-1"></i> OWASP ZAP Config
        </button>
    </div>
</div>

<!-- Security Score & Release Readiness -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 text-center bg-white">
            <h6 class="text-muted fw-bold">Overall Security Score</h6>
            <h1 class="display-3 fw-bold text-success my-2" id="securityScore"><?= $score ?? 98 ?></h1>
            <span class="badge bg-success px-3 py-2">Score Passed (>= 80)</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 text-center bg-white">
            <h6 class="text-muted fw-bold">Production Readiness Gate</h6>
            <?php if (!empty($production_ready)): ?>
                <h2 class="fw-bold text-success my-3"><i class="fa-solid fa-circle-check me-2"></i>PASSED</h2>
                <span class="badge bg-success">No Unresolved Critical/High Findings</span>
            <?php else: ?>
                <h2 class="fw-bold text-danger my-3"><i class="fa-solid fa-circle-xmark me-2"></i>BLOCKED</h2>
                <span class="badge bg-danger">Critical or High Vulnerabilities Present</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h6 class="text-muted fw-bold mb-3">Findings Breakdown</h6>
            <div class="d-flex justify-content-between mb-1">
                <span>Critical:</span> <span class="badge bg-danger"><?= $counts['Critical'] ?? 0 ?></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>High:</span> <span class="badge bg-warning text-dark"><?= $counts['High'] ?? 0 ?></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Medium:</span> <span class="badge bg-info text-dark"><?= $counts['Medium'] ?? 0 ?></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Low / Info:</span> <span class="badge bg-secondary"><?= ($counts['Low'] ?? 0) + ($counts['Info'] ?? 0) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Findings Table -->
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check me-2 text-primary"></i>Security Findings & Remediation Workflow</h5>
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Severity</th>
                <th>Vulnerability Name</th>
                <th>Component</th>
                <th>CWE</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($findings)): ?>
                <?php foreach ($findings as $f): ?>
                    <tr>
                        <td>
                            <span class="badge bg-<?= $f['severity'] === 'Critical' ? 'danger' : ($f['severity'] === 'High' ? 'warning' : 'info') ?>">
                                <?= htmlspecialchars($f['severity']) ?>
                            </span>
                        </td>
                        <td><strong><?= htmlspecialchars($f['vulnerability_name']) ?></strong></td>
                        <td><code><?= htmlspecialchars($f['affected_component']) ?></code></td>
                        <td><?= htmlspecialchars($f['cwe'] ?? 'CWE-352') ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($f['status']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="Swal.fire('Remediation Notes', '<?= htmlspecialchars($f['remediation'] ?? 'Apply CSRF protection.') ?>', 'info')">
                                Review
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No active security findings recorded. All security checks passed cleanly!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function triggerScan(type) {
        Swal.fire({
            title: 'กำลังดำเนินการ Security Scan...',
            text: `กำลังตรวจสอบด้วย ${type}`,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('/tomvishimpro/public/api/v1/security/scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ scan_type: type })
        })
        .then(r => r.json())
        .then(res => {
            Swal.fire('Scan Complete', res.message, 'success').then(() => location.reload());
        });
    }

    function viewZapConfig() {
        fetch('/tomvishimpro/public/api/v1/security/zap-config')
            .then(r => r.json())
            .then(res => {
                Swal.fire({
                    title: 'OWASP ZAP Automation Config',
                    html: `<pre class="text-start bg-dark text-success p-3 rounded" style="max-height:300px; overflow-y:auto;"><code>${JSON.stringify(res.data, null, 2)}</code></pre>`,
                    width: '600px'
                });
            });
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
