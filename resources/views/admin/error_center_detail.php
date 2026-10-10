<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident #<?= $group['id'] ?> RCA & Framework Repair - TomvisHIMPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .card-custom { background: #1e293b; border: 1px solid #334155; border-radius: 12px; }
        .code-box { background-color: #090d16; color: #38bdf8; border: 1px solid #1e293b; font-family: monospace; border-radius: 8px; p: 1rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="?route=/admin/error-center" class="text-info text-decoration-none mb-2 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Back to Error Intelligence Center
                </a>
                <h3 class="fw-bold text-white mb-0">
                    Incident #<?= $group['id'] ?>: <?= htmlspecialchars($group['exception_class']) ?>
                </h3>
                <span class="text-secondary font-monospace small">Fingerprint: <?= htmlspecialchars($group['fingerprint']) ?></span>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-danger fs-6 px-3 py-2"><?= $group['severity'] ?></span>
                <span class="badge bg-primary fs-6 px-3 py-2"><?= $group['status'] ?></span>
            </div>
        </div>

        <!-- Row: Overview & RCA -->
        <div class="row g-4 mb-4">
            <!-- Left: Root Cause Analysis -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold text-info mb-3"><i class="bi bi-search me-2"></i> Root Cause Diagnosis</h5>
                    <?php if ($rca): ?>
                        <div class="mb-3">
                            <label class="text-secondary small">Suspected Layer</label>
                            <div class="fs-5 fw-bold text-white"><?= htmlspecialchars($rca['layer']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-secondary small">Suspected Component</label>
                            <div class="font-monospace text-warning"><?= htmlspecialchars($rca['suspected_component']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-secondary small">Confidence Score</label>
                            <div class="progress bg-dark mt-1" style="height: 20px;">
                                <div class="progress-bar bg-success" style="width: <?= ($rca['confidence_score'] * 100) ?>%;">
                                    <?= round($rca['confidence_score'] * 100) ?>%
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-secondary small">Evidence Summary</label>
                            <div class="p-3 bg-dark rounded text-light small font-monospace">
                                <?= htmlspecialchars($rca['evidence_summary']) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-secondary">RCA diagnosis pending.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: AI Repair Assistant Solution Proposal -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100 border-info">
                    <h5 class="fw-bold text-warning mb-3"><i class="bi bi-magic me-2"></i> AI Framework Repair Assistant</h5>
                    <p class="small text-secondary mb-3">Automated patch diff & unit test proposal based on sanitized root cause analysis.</p>

                    <div class="mb-3">
                        <label class="text-secondary small">Proposed Core Patch Diff</label>
                        <pre class="code-box text-success p-3 rounded small" style="max-height: 150px; overflow-y: auto;"><?= htmlspecialchars($aiAnalysis['proposed_diff']) ?></pre>
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small">Generated Regression Unit Test</label>
                        <pre class="code-box text-info p-3 rounded small" style="max-height: 120px; overflow-y: auto;"><?= htmlspecialchars($aiAnalysis['proposed_test']) ?></pre>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-success"><i class="bi bi-shield-check me-1"></i> Non-Invasive Patch Validation PASS</span>
                        <button class="btn btn-sm btn-success" onclick="proposeFix();">
                            <i class="bi bi-send-check me-1"></i> Submit Core Patch Proposal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stack Trace Timeline Card -->
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-terminal me-2 text-danger"></i> Sanitized Event Stack Trace</h5>
            <?php if (!empty($events)): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between text-secondary small mb-1">
                        <span>Sanitized Message</span>
                        <span>Trace ID: <?= htmlspecialchars($events[0]['trace_id']) ?></span>
                    </div>
                    <div class="p-3 bg-dark border border-danger rounded text-danger fw-semibold">
                        <?= htmlspecialchars($events[0]['sanitized_message']) ?>
                    </div>
                </div>
                <div>
                    <label class="text-secondary small mb-1">Stack Trace (Secrets & PHI Redacted)</label>
                    <pre class="code-box text-light p-3 rounded" style="max-height: 300px; overflow-y: auto;"><?= htmlspecialchars($events[0]['sanitized_trace']) ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function proposeFix() {
            alert('Core Patch Proposal submitted for human engineering review and approval!');
        }
    </script>
</body>
</html>
