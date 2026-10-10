<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Error Intelligence & Framework Repair Platform - TomvisHIMPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .navbar-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-bottom: 1px solid #334155; }
        .card-metric { background: #1e293b; border: 1px solid #334155; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .card-metric:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
        .badge-critical { background-color: #ef4444; color: #fff; }
        .badge-high { background-color: #f97316; color: #fff; }
        .badge-medium { background-color: #eab308; color: #000; }
        .badge-low { background-color: #3b82f6; color: #fff; }
        .badge-core { background-color: #8b5cf6; color: #fff; }
        .table-custom { background-color: #1e293b; color: #f8fafc; border-color: #334155; }
        .table-custom th { background-color: #0f172a; color: #94a3b8; border-color: #334155; font-weight: 600; }
        .table-custom td { border-color: #334155; vertical-align: middle; }
        .btn-brand { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; border: none; }
        .btn-brand:hover { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 py-3">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="?route=/admin/error-center">
                <i class="bi bi-shield-exclamation text-danger fs-3"></i>
                <span class="fw-bold fs-4">Central Error Intelligence</span>
                <span class="badge bg-primary ms-2">v3.0 Core</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="?route=/dashboard" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
                <a href="?route=/developer" class="btn btn-outline-info btn-sm"><i class="bi bi-code-slash me-1"></i> Developer Studio</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <!-- Subheading & Banner -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1"><i class="bi bi-cpu text-info me-2"></i> Error Control Center & Automated Framework Repair</h2>
                <p class="text-secondary mb-0">Detect Once &rarr; Analyze Centrally &rarr; Fix at Source &rarr; Test Everywhere &rarr; Release Safely</p>
            </div>
            <button class="btn btn-brand px-3 py-2 fw-semibold" onclick="location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Intelligence
            </button>
        </div>

        <!-- Metric Cards Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card card-metric p-3">
                    <div class="text-secondary small font-monospace">TOTAL ERRORS</div>
                    <div class="fs-2 fw-bold text-white mt-1"><?= number_format($metrics['total_errors'] ?? 0) ?></div>
                    <div class="small text-info mt-1"><i class="bi bi-activity me-1"></i> Lifetime Captured</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-metric p-3 border-warning">
                    <div class="text-warning small font-monospace">NEW UNTRIAGED</div>
                    <div class="fs-2 fw-bold text-warning mt-1"><?= number_format($metrics['new_issues'] ?? 0) ?></div>
                    <div class="small text-secondary mt-1"><i class="bi bi-exclamation-circle me-1"></i> Pending Triage</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-metric p-3 border-danger">
                    <div class="text-danger small font-monospace">CRITICAL INCIDENTS</div>
                    <div class="fs-2 fw-bold text-danger mt-1"><?= number_format($metrics['critical_incidents'] ?? 0) ?></div>
                    <div class="small text-danger mt-1"><i class="bi bi-lightning-charge me-1"></i> Immediate Priority</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-metric p-3">
                    <div class="text-info small font-monospace">RECURRING ISSUES</div>
                    <div class="fs-2 fw-bold text-info mt-1"><?= number_format($metrics['recurring_issues'] ?? 0) ?></div>
                    <div class="small text-secondary mt-1"><i class="bi bi-arrow-repeat me-1"></i> Deduplicated</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-metric p-3">
                    <div class="text-purple small font-monospace" style="color: #a78bfa;">CORE DEFECTS</div>
                    <div class="fs-2 fw-bold text-purple mt-1" style="color: #c4b5fd;"><?= number_format($metrics['core_defects'] ?? 0) ?></div>
                    <div class="small text-secondary mt-1"><i class="bi bi-box-seam me-1"></i> Framework Core</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-metric p-3">
                    <div class="text-success small font-monospace">AFFECTED APPS</div>
                    <div class="fs-2 fw-bold text-success mt-1"><?= number_format($metrics['affected_apps'] ?? 1) ?></div>
                    <div class="small text-success mt-1"><i class="bi bi-diagram-3 me-1"></i> Connected Systems</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card card-metric p-3 mb-4">
            <form method="GET" action="index.php" class="row g-3 align-items-center">
                <input type="hidden" name="route" value="/admin/error-center">
                <div class="col-md-3">
                    <label class="form-label text-secondary small mb-1">Filter by Severity</label>
                    <select name="severity" class="form-select bg-dark text-white border-secondary">
                        <option value="">All Severities</option>
                        <option value="Critical" <?= ($filterSeverity === 'Critical') ? 'selected' : '' ?>>Critical</option>
                        <option value="High" <?= ($filterSeverity === 'High') ? 'selected' : '' ?>>High</option>
                        <option value="Medium" <?= ($filterSeverity === 'Medium') ? 'selected' : '' ?>>Medium</option>
                        <option value="Low" <?= ($filterSeverity === 'Low') ? 'selected' : '' ?>>Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small mb-1">Filter by Status</label>
                    <select name="status" class="form-select bg-dark text-white border-secondary">
                        <option value="">All Statuses</option>
                        <option value="Detected" <?= ($filterStatus === 'Detected') ? 'selected' : '' ?>>Detected</option>
                        <option value="Triaged" <?= ($filterStatus === 'Triaged') ? 'selected' : '' ?>>Triaged</option>
                        <option value="Investigating" <?= ($filterStatus === 'Investigating') ? 'selected' : '' ?>>Investigating</option>
                        <option value="Fix Proposed" <?= ($filterStatus === 'Fix Proposed') ? 'selected' : '' ?>>Fix Proposed</option>
                        <option value="Approved" <?= ($filterStatus === 'Approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="Released" <?= ($filterStatus === 'Released') ? 'selected' : '' ?>>Released</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2 mt-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
                    <a href="?route=/admin/error-center" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Reset</a>
                </div>
            </form>
        </div>

        <!-- Incidents Table -->
        <div class="card card-metric p-0 overflow-hidden">
            <div class="card-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-list-task me-2 text-primary"></i> Fingerprinted Incident Groups</h5>
                <span class="badge bg-secondary"><?= count($incidents) ?> Active Fingerprints</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fingerprint</th>
                            <th>Exception & Component</th>
                            <th>Severity</th>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Apps</th>
                            <th>Status</th>
                            <th>Last Seen</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incidents)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-secondary">
                                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                    No errors match the current criteria. System healthy.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($incidents as $inc): ?>
                                <tr>
                                    <td class="font-monospace">#<?= $inc['id'] ?></td>
                                    <td>
                                        <span class="font-monospace text-info small" title="<?= htmlspecialchars($inc['fingerprint']) ?>">
                                            <?= substr($inc['fingerprint'], 0, 12) ?>...
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($inc['exception_class']) ?></div>
                                        <small class="text-secondary font-monospace"><?= htmlspecialchars(basename(str_replace('\\', '/', $inc['source_component']))) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                            $sevClass = match($inc['severity']) {
                                                'Critical' => 'badge-critical',
                                                'High' => 'badge-high',
                                                'Medium' => 'badge-medium',
                                                default => 'badge-low'
                                            };
                                        ?>
                                        <span class="badge <?= $sevClass ?>"><?= $inc['severity'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-light"><?= $inc['category'] ?></span>
                                    </td>
                                    <td class="fw-bold text-warning"><?= number_format($inc['occurrence_count']) ?></td>
                                    <td class="text-info"><?= $inc['affected_apps_count'] ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?= $inc['status'] ?></span>
                                    </td>
                                    <td class="small text-secondary"><?= date('H:i:s M d', strtotime($inc['last_seen_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="?route=/admin/error-center/incident&id=<?= $inc['id'] ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-search me-1"></i> Detail & RCA
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
