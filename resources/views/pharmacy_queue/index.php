<?php
$title = "PDH Pharmacy Queue System - TomvisHIMPro 3.0";
ob_start();
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold"><i class="fa-solid fa-users-line text-success me-2"></i>PDH Pharmacy Queue Counter Management</h3>
        <p class="text-muted">ระบบบริหารจัดการคิวห้องยา (Reference Module - Single Framework)</p>
    </div>
    <div>
        <a href="/tomvishimpro/public/pharmacy-queue/tv-display" target="_blank" class="btn btn-dark fw-bold">
            <i class="fa-solid fa-tv me-1"></i> Open TV Display
        </a>
        <button class="btn btn-success fw-bold ms-2" onclick="issueNewQueue()">
            <i class="fa-solid fa-ticket me-1"></i> ออกบัตรคิวใหม่
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white">
            <span class="text-muted fw-bold">คิวรอรับบริการทั้งหมด</span>
            <h1 class="fw-bold text-primary my-2">12</h1>
            <span class="badge bg-primary">Waiting</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white">
            <span class="text-muted fw-bold">ช่องบริการ 1 (เรียกคิวปัจจุบัน)</span>
            <h1 class="fw-bold text-success my-2" id="callingQueue1">A001</h1>
            <span class="badge bg-success">Calling</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white">
            <span class="text-muted fw-bold">ช่องบริการ 2 (จัดยาเสร็จ)</span>
            <h1 class="fw-bold text-info my-2">A002</h1>
            <span class="badge bg-info">Dispensing</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white">
            <span class="text-muted fw-bold">เวลารอเฉลี่ย</span>
            <h1 class="fw-bold text-dark my-2">4.5 <small class="fs-6">นาที</small></h1>
            <span class="badge bg-secondary">Average Wait</span>
        </div>
    </div>
</div>

<!-- Active Queues Table -->
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-ol text-primary me-2"></i>รายการคิวรอเรียกรับยา</h5>
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>หมายเลขคิว</th>
                <th>HN</th>
                <th>ชื่อ-นามสกุล ผู้ป่วย</th>
                <th>ช่องบริการ</th>
                <th>สถานะ</th>
                <th>เวลาออกคิว</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody id="tableQueueBody">
            <tr>
                <td><span class="badge bg-primary fs-5">A001</span></td>
                <td>67000001</td>
                <td><strong>สมชาย ใจดี</strong></td>
                <td><span class="badge bg-success fs-6">ช่อง 1</span></td>
                <td><span class="badge bg-success">Calling</span></td>
                <td>10:45:12</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="callQueue('A001', 1)"><i class="fa-solid fa-volume-high me-1"></i> เรียกคิว</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="completeQueue('A001')">จ่ายยาเรียบร้อย</button>
                </td>
            </tr>
            <tr>
                <td><span class="badge bg-primary fs-5">A002</span></td>
                <td>67000002</td>
                <td><strong>สมศรี มีสุข</strong></td>
                <td><span class="badge bg-info fs-6">ช่อง 2</span></td>
                <td><span class="badge bg-info">Dispensing</span></td>
                <td>10:48:30</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="callQueue('A002', 2)"><i class="fa-solid fa-volume-high me-1"></i> เรียกคิว</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="completeQueue('A002')">จ่ายยาเรียบร้อย</button>
                </td>
            </tr>
            <tr>
                <td><span class="badge bg-secondary fs-5">B001</span></td>
                <td>67000003</td>
                <td><strong>วันดี บุญมี</strong></td>
                <td><span class="badge bg-secondary">-</span></td>
                <td><span class="badge bg-warning text-dark">Waiting</span></td>
                <td>10:50:00</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="callQueue('B001', 1)"><i class="fa-solid fa-volume-high me-1"></i> เรียกเข้าช่อง 1</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function issueNewQueue() {
        fetch('/tomvishimpro/public/api/v1/pharmacy-queue/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ hn: '67000001', queue_type: 'A' })
        })
        .then(r => r.json())
        .then(res => {
            Swal.fire('ออกบัตรคิวสำเร็จ!', `หมายเลขคิว: ${res.data.queue_no}`, 'success');
        });
    }

    function callQueue(no, counter) {
        fetch('/tomvishimpro/public/api/v1/pharmacy-queue/call', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ queue_no: no, counter: counter })
        })
        .then(r => r.json())
        .then(res => {
            Swal.fire('กำลังเรียกคิว', `คิว ${no} กรุณาติดต่อช่องบริการ ${counter}`, 'info');
            document.getElementById('callingQueue1').innerText = no;
        });
    }

    function completeQueue(no) {
        Swal.fire('จ่ายยาเรียบร้อย', `คิว ${no} ทำการจ่ายยาเสร็จสิ้น`, 'success');
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
