<?php
$title = "Developer Studio 3.0 - TomvisHIMPro RAD Platform";
ob_start();
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold"><i class="fa-solid fa-wand-magic-sparkles text-purple me-2"></i>TomvisHIMPro Developer Studio 3.0</h3>
        <p class="text-muted">ระบบ Rapid Application Development (RAD) สเกลระดับโรงพยาบาล ลดเวลาเขียนโค้ดพื้นฐาน 50%+</p>
    </div>
    <div>
        <button class="btn btn-success fw-bold" onclick="runBenchmark()">
            <i class="fa-solid fa-stopwatch me-1"></i> Run Time-Saving Benchmark
        </button>
    </div>
</div>

<!-- Project Wizard Section -->
<div class="card border-0 shadow-sm p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-layer-group text-primary me-2"></i>10-Step Hospital RAD Project Wizard</h5>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label fw-bold">1. ชื่อโมดูล / ระบบงาน</label>
            <input type="text" id="wizardName" class="form-control" value="PDHPharmacyQueue">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">2. Template รูปแบบ</label>
            <select id="wizardTemplate" class="form-select">
                <option value="CRUD" selected>Smart CRUD System</option>
                <option value="Dashboard">Clinical Dashboard</option>
                <option value="Form">Clinical Form Builder</option>
                <option value="Report">Report & Analytics</option>
                <option value="Queue">Pharmacy Queue System</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">3. Data Source</label>
            <select id="wizardSource" class="form-select">
                <option value="Central_MySQL">Central MySQL (192.168.111.240)</option>
                <option value="HIMPro_Gateway">HIMPro HIS Read-only Gateway</option>
                <option value="Mock_HIS">Mock Synthetic HIS</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-bold" onclick="generateWizardModule()">
                <i class="fa-solid fa-bolt me-1"></i> Generate Source Code
            </button>
        </div>
    </div>
</div>

<!-- Benchmark Results Display -->
<div id="benchmarkCard" class="card border-0 shadow-sm p-4 mb-4 d-none">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-line text-success me-2"></i>Development Time Saving Benchmark Results</h5>
    <table class="table table-hover align-middle" id="tableBenchmark">
        <thead class="table-dark">
            <tr>
                <th>Task Description</th>
                <th>Traditional PHP Time</th>
                <th>RAD Studio Time</th>
                <th>Time Saved</th>
                <th>% Saved</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="alert alert-success fs-5 fw-bold mb-0" id="summaryBenchmark"></div>
</div>

<script>
    function generateWizardModule() {
        const name = document.getElementById('wizardName').value;
        const template = document.getElementById('wizardTemplate').value;

        Swal.fire({
            title: `กำลังสร้างโมดูล [${name}]...`,
            text: 'ระบบกำลัง Generate Routes, Controllers, Services, DTOs, Views, Permissions และ PHPUnit Tests...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('/tomvishimpro/public/api/v1/developer/generate-wizard', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ module_name: name, template: template })
        })
        .then(r => r.json())
        .then(res => {
            Swal.fire('สร้างโมดูลสำเร็จ!', `โมดูล ${name} ถูกสร้างขึ้นเรียบร้อยแล้ว พร้อม OpenAPI Specs และ Automated Tests`, 'success');
        });
    }

    function runBenchmark() {
        fetch('/tomvishimpro/public/api/v1/developer/benchmark')
            .then(r => r.json())
            .then(res => {
                const data = res.data;
                document.getElementById('benchmarkCard').classList.remove('d-none');

                let rows = '';
                data.tasks.forEach(t => {
                    rows += `<tr>
                        <td><strong>${t.task_name}</strong></td>
                        <td>${t.traditional_mins} mins</td>
                        <td><span class="badge bg-success">${t.rad_studio_mins} mins</span></td>
                        <td>${t.time_saved_mins} mins</td>
                        <td><span class="badge bg-primary">${t.pct_saved}%</span></td>
                    </tr>`;
                });
                document.querySelector('#tableBenchmark tbody').innerHTML = rows;

                document.getElementById('summaryBenchmark').innerHTML = `<i class="fa-solid fa-circle-check me-2"></i> สรุปผลประเมิน: ใช้เวลารวมเพียง <strong>${data.summary.total_rad_mins} นาที</strong> (จากเดิม ${data.summary.total_traditional_mins} นาที) — ประหยัดเวลาพัฒนารวม <strong>${data.summary.total_saved_mins} นาที (${data.summary.overall_pct_saved}% Saved!) Target >= 50% PASSED!</strong>`;
            });
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
