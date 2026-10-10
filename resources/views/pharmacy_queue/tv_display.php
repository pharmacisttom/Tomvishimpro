<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Display - PDH Pharmacy Queue</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
            min-height: 100vh;
        }
        .header-bar {
            background-color: #0056b3;
            padding: 1.5rem 3rem;
        }
        .queue-display-box {
            background: #1e293b;
            border-radius: 1rem;
            border: 2px solid #38bdf8;
            padding: 2.5rem;
            text-align: center;
        }
        .queue-number {
            font-size: 6rem;
            font-weight: 800;
            color: #38bdf8;
        }
        .counter-number {
            font-size: 3.5rem;
            font-weight: 700;
            color: #4ade80;
        }
    </style>
</head>
<body>
    <div class="header-bar d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0">โรงพยาบาลผาขาว — คิวรับยาห้องยา OPD</h1>
            <p class="mb-0 text-white-50">PDH Pharmacy Queue Display System</p>
        </div>
        <div class="text-end">
            <h2 class="fw-bold mb-0" id="clock">10:45:00</h2>
            <span class="badge bg-success fs-6">เปิดบริการปกติ</span>
        </div>
    </div>

    <div class="container-fluid px-5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="queue-display-box">
                    <h3 class="fw-bold text-white-50">ช่องบริการ 1 (รับยา)</h3>
                    <div class="queue-number" id="tvQueue1">A001</div>
                    <div class="counter-number">ช่องบริการ 1</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="queue-display-box" style="border-color: #4ade80;">
                    <h3 class="fw-bold text-white-50">ช่องบริการ 2 (คำปรึกษาด้านยา)</h3>
                    <div class="queue-number" style="color: #4ade80;" id="tvQueue2">A002</div>
                    <div class="counter-number">ช่องบริการ 2</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('th-TH');
        }, 1000);
    </script>
</body>
</html>
