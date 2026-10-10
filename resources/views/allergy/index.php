<?php
$title = "Drug Allergy & Naranjo ADR Assessment - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-allergies text-warning me-2"></i>Drug Allergy & Naranjo ADR Assessment</h3>
    <p class="text-muted">ประเมินความสัมพันธ์ของการเกิดประวัติแพ้ยาและอาการไม่พึงประสงค์จากยา (Naranjo Algorithm)</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-clipboard-question text-primary me-2"></i>แบบประเมิน Naranjo Algorithm (10 Questions)</h5>
            <form id="naranjoForm">
                <div class="mb-3">
                    <label class="form-label fw-bold">1. เคยมีรายงานสรุปที่แน่นอนเกี่ยวกับปฏิกิริยานี้มาก่อนหรือไม่?</label>
                    <select name="q1" class="form-select">
                        <option value="1">ใช่ (+1)</option>
                        <option value="0" selected>ไม่ใช่ (0)</option>
                        <option value="0">ไม่ทราบ (0)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">2. อาการไม่พึงประสงค์เกิดขึ้นหลังได้รับยาที่สงสัยหรือไม่?</label>
                    <select name="q2" class="form-select">
                        <option value="2" selected>ใช่ (+2)</option>
                        <option value="0">ไม่ใช่ (0)</option>
                        <option value="0">ไม่ทราบ (0)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">3. อาการดีขึ้นเมื่อหยุดยา หรือเมื่อให้ยาต้านเฉพาะหรือไม่?</label>
                    <select name="q3" class="form-select">
                        <option value="1" selected>ใช่ (+1)</option>
                        <option value="0">ไม่ใช่ (0)</option>
                        <option value="0">ไม่ทราบ (0)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">4. อาการเกิดขึ้นอีกครั้งเมื่อได้รับยาเดิมซ้ำหรือไม่?</label>
                    <select name="q4" class="form-select">
                        <option value="2">ใช่ (+2)</option>
                        <option value="0" selected>ไม่ใช่ (0)</option>
                        <option value="0">ไม่ทราบ (0)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">5. มีสาเหตุอื่นที่ทำให้เกิดอาการนี้ได้เองหรือไม่?</label>
                    <select name="q5" class="form-select">
                        <option value="-1">ใช่ (-1)</option>
                        <option value="0" selected>ไม่ใช่ (0)</option>
                        <option value="0">ไม่ทราบ (0)</option>
                    </select>
                </div>
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-warning btn-lg fw-bold" onclick="calculateNaranjo()">
                        <i class="fa-solid fa-calculator me-1"></i>คำนวณผลประเมิน Naranjo Score
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white text-center">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-column me-2 text-warning"></i>ผลการประเมินความน่าจะเป็น (Probability Result)</h5>

            <div class="p-4 rounded border mb-4" id="scoreBox" style="background-color: #fff3cd;">
                <h1 class="display-3 fw-bold text-dark mb-0" id="totalScore">3</h1>
                <p class="lead fw-bold text-dark mb-0" id="scoreCategory">Possible (น่าจะใช่)</p>
            </div>

            <div class="text-start">
                <h6 class="fw-bold mb-2">เกณฑ์การจัดระดับความสัมพันธ์ (Criteria):</h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Definite (ใช่แน่นอน)</strong>
                        <span class="badge bg-danger rounded-pill">>= 9 คะแนน</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Probable (น่าจะใช่)</strong>
                        <span class="badge bg-warning rounded-pill">5 - 8 คะแนน</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Possible (อาจจะใช่)</strong>
                        <span class="badge bg-info rounded-pill">1 - 4 คะแนน</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Doubtful (ยังข้อสงสัย)</strong>
                        <span class="badge bg-secondary rounded-pill"><= 0 คะแนน</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateNaranjo() {
        const form = document.getElementById('naranjoForm');
        const formData = new FormData(form);

        fetch('/tomvishimpro/public/api/v1/allergy/naranjo', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                document.getElementById('totalScore').innerText = res.data.total_score;
                document.getElementById('scoreCategory').innerText = res.data.category;
                Swal.fire('ประเมินสำเร็จ', `คะแนนรวม Naranjo Score: ${res.data.total_score} (${res.data.category})`, 'success');
            }
        });
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
