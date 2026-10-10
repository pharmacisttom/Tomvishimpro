<?php
$title = "Performance & Monitoring Center - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-gauge text-danger me-2"></i>Performance & Monitoring Center</h3>
    <p class="text-muted">เฝ้าระวังประสิทธิภาพ Application Health, Database Latency, HIS Connection และ Memory Usage</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted fw-bold">Application Health</span>
            <h2 class="fw-bold text-success my-2"><i class="fa-solid fa-heart-pulse me-1"></i>HEALTHY</h2>
            <span class="badge bg-success">Uptime: 99.99%</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted fw-bold">Central DB Latency</span>
            <h2 class="fw-bold text-primary my-2">1.2 ms</h2>
            <span class="badge bg-primary">192.168.111.240</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted fw-bold">Slow Queries (Last 24h)</span>
            <h2 class="fw-bold text-dark my-2">0</h2>
            <span class="badge bg-success">No Slow Queries</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted fw-bold">Memory Utilization</span>
            <h2 class="fw-bold text-purple my-2">8.0 MB</h2>
            <span class="badge bg-purple text-white">PHP Peak Usage</span>
        </div>
    </div>
</div>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
