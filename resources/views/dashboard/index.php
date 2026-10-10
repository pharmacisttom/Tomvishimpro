<?php
$title = "Dashboard - TomvisHIMPro";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-gauge-high text-primary me-2"></i>Hospital Dashboard</h3>
        <p class="text-muted mb-0">ระบบบริหารจัดการข้อมูลสุขภาพและเชื่อมต่อ HIMPro HIS Sandbox</p>
    </div>
    <div>
        <span class="badge bg-primary fs-6 px-3 py-2"><i class="fa-solid fa-calendar-day me-1"></i> ปีงบประมาณ 2569</span>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold">ผู้ป่วยทั้งหมด (Patients)</span>
                    <h2 class="fw-bold text-dark mb-0"><?= $total_patients ?? 50 ?></h2>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="fa-solid fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold">จำนวนการมารับบริการ (Visits)</span>
                    <h2 class="fw-bold text-success mb-0"><?= $total_visits ?? 128 ?></h2>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="fa-solid fa-hospital-user fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold">ประวัติแพ้ยา (Allergies)</span>
                    <h2 class="fw-bold text-warning mb-0"><?= $total_allergies ?? 4 ?></h2>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold">Security Score</span>
                    <h2 class="fw-bold text-danger mb-0"><?= $security_score ?? 98 ?> / 100</h2>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-circle">
                    <i class="fa-solid fa-shield-halved fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-3">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i>แนวโน้มผู้ป่วยมารับบริการรายเดือน (Monthly Visits)</h5>
            <canvas id="visitChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-pie text-warning me-2"></i>สัดส่วนกลุ่มการแพ้ยา (Top Allergies)</h5>
            <canvas id="allergyChart" height="220"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Line Chart
        const ctxLine = document.getElementById('visitChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค.', 'ก.พ.', 'มี.ค.'],
                datasets: [{
                    label: 'จำนวนผู้ป่วย OPD',
                    data: [12, 18, 25, 30, 22, 21],
                    borderColor: '#0056b3',
                    backgroundColor: 'rgba(0, 86, 179, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // Pie Chart
        const ctxPie = document.getElementById('allergyChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Penicillin', 'Co-trimoxazole', 'Aspirin'],
                datasets: [{
                    data: [2, 1, 1],
                    backgroundColor: ['#dc3545', '#ffc107', '#0d6efd']
                }]
            }
        });
    });
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
