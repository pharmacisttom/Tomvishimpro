<?php
$title = "Module Registry - TomvisHIMPro";
ob_start();
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="fa-solid fa-cubes text-purple me-2"></i>Module Registry & Version Compatibility Matrix</h3>
    <p class="text-muted">รายการโมดูลในระบบและสถานะความเข้ากันได้ (Plugin Architecture)</p>
</div>

<div class="card border-0 shadow-sm p-4">
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Module Name</th>
                <th>Slug</th>
                <th>Version</th>
                <th>Framework Requirement</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Framework Core</strong></td>
                <td><code>core</code></td>
                <td>3.0.0</td>
                <td>>= 3.0.0</td>
                <td><span class="badge bg-success">Active System</span></td>
                <td><span class="text-muted">System Core</span></td>
            </tr>
            <tr>
                <td><strong>PDH Pharmacy Queue System</strong></td>
                <td><code>pharmacy-queue</code></td>
                <td>1.0.0</td>
                <td>>= 3.0.0</td>
                <td><span class="badge bg-success">Active Module</span></td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="location.href='/tomvishimpro/public/pharmacy-queue'">Open Module</button></td>
            </tr>
            <tr>
                <td><strong>HIMPro HIS Gateway</strong></td>
                <td><code>his-gateway</code></td>
                <td>3.0.0</td>
                <td>>= 3.0.0</td>
                <td><span class="badge bg-success">Active Module</span></td>
                <td><button class="btn btn-sm btn-outline-secondary">Configure</button></td>
            </tr>
            <tr>
                <td><strong>Sandbox Studio 3.0</strong></td>
                <td><code>sandbox</code></td>
                <td>3.0.0</td>
                <td>>= 3.0.0</td>
                <td><span class="badge bg-success">Active Module</span></td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="location.href='/tomvishimpro/public/sandbox'">Open Studio</button></td>
            </tr>
        </tbody>
    </table>
</div>

<?php
$view_content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
