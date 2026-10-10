<?php
$title = "API Generator & OpenAPI Explorer - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-cloud-code text-info me-2"></i>Smart API Generator & OpenAPI Explorer</h3>
    <p class="text-muted">สำรวจและทดสอบ RESTful APIs ทั้งหมดในระบบ TomvisHIMPro 3.0</p>
</div>

<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list me-2 text-primary"></i>Available REST API Endpoints</h5>
    <div class="list-group">
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-success me-2">GET</span> <code>/api/v1/health</code>
                <span class="text-muted ms-2">System Health Check API</span>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="testApi('/tomvishimpro/public/api/v1/health')">Test API</button>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-success me-2">GET</span> <code>/api/v1/patients/67000001</code>
                <span class="text-muted ms-2">Get Patient Demographic & Visit History</span>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="testApi('/tomvishimpro/public/api/v1/patients/67000001')">Test API</button>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-success me-2">GET</span> <code>/api/v1/pharmacy-queue/list</code>
                <span class="text-muted ms-2">PDH Pharmacy Queue Active List API</span>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="testApi('/tomvishimpro/public/api/v1/pharmacy-queue/list')">Test API</button>
        </div>
    </div>

    <div class="mt-4">
        <h6 class="fw-bold mb-2">API Response JSON Output:</h6>
        <pre class="bg-dark text-success p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code id="apiOutput">// Click 'Test API' above to execute request...</code></pre>
    </div>
</div>

<script>
    function testApi(url) {
        document.getElementById('apiOutput').innerText = `[*] Requesting ${url}...`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                document.getElementById('apiOutput').innerText = JSON.stringify(data, null, 4);
            });
    }
</script>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
