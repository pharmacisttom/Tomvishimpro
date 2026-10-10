<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'TomvisHIMPro 3.0 RAD Platform') ?></title>
    <!-- Sarabun Font -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-hospital: #0056b3;
            --primary-dark: #003d82;
            --secondary-pharmacy: #198754;
            --bg-neutral: #f4f6f9;
            --sidebar-bg: #0f172a;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--bg-neutral);
            color: #334155;
        }

        #sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        #sidebar .brand {
            padding: 1.25rem 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #38bdf8;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-section {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            padding: 1rem 1.25rem 0.3rem;
            letter-spacing: 0.05em;
        }

        #sidebar .nav-link {
            color: #94a3b8;
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
            border-radius: 0.375rem;
            margin: 0.1rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #fff;
            background: rgba(56, 189, 248, 0.15);
        }

        #sidebar .nav-link i {
            width: 20px;
        }

        #main-content {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
        }

        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 2rem;
            margin-left: 260px;
        }

        .badge-rad {
            background-color: #8b5cf6;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div id="sidebar">
        <div class="brand">
            <i class="fa-solid fa-hospital me-2"></i>TomvisHIMPro
            <span class="badge badge-rad ms-1">v3.0 RAD</span>
        </div>
        <ul class="nav flex-column mt-2 mb-4">
            <div class="nav-section">Clinical Applications</div>
            <li class="nav-item">
                <a href="/tomvishimpro/public/dashboard" class="nav-link">
                    <i class="fa-solid fa-chart-line text-info"></i> Hospital Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/patients/search" class="nav-link">
                    <i class="fa-solid fa-user-injured text-primary"></i> Patient Search
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/allergy" class="nav-link">
                    <i class="fa-solid fa-allergies text-warning"></i> Drug Allergy / ADR
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/medrec" class="nav-link">
                    <i class="fa-solid fa-pills text-success"></i> Med Reconciliation
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/pharmacy-queue" class="nav-link">
                    <i class="fa-solid fa-users-line text-success"></i> PDH Pharmacy Queue
                </a>
            </li>

            <div class="nav-section">Developer Studio 3.0</div>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer" class="nav-link">
                    <i class="fa-solid fa-wand-magic-sparkles text-purple"></i> Developer Studio
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer/api-explorer" class="nav-link">
                    <i class="fa-solid fa-cloud-code text-info"></i> API Generator & Explorer
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer/reports" class="nav-link">
                    <i class="fa-solid fa-chart-pie text-success"></i> Smart Report Builder
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/sandbox" class="nav-link">
                    <i class="fa-solid fa-vial text-warning"></i> Synthetic HIS Sandbox
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer/tests" class="nav-link">
                    <i class="fa-solid fa-list-check text-success"></i> Automated Test Runner
                </a>
            </li>

            <div class="nav-section">DevSecOps & Operations</div>
            <li class="nav-item">
                <a href="/tomvishimpro/public/security" class="nav-link">
                    <i class="fa-solid fa-shield-halved text-danger"></i> Security & Pentest Lab
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer/deployment" class="nav-link">
                    <i class="fa-solid fa-rocket text-primary"></i> Zero-Reconfig Deploy
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/admin/monitoring" class="nav-link">
                    <i class="fa-solid fa-gauge text-danger"></i> Monitoring Center
                </a>
            </li>
            <li class="nav-item">
                <a href="/tomvishimpro/public/developer/modules" class="nav-link">
                    <i class="fa-solid fa-cubes text-purple"></i> Module Registry
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <div class="top-navbar d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted"><i class="fa-solid fa-server me-1"></i> Central MySQL: <strong>192.168.111.240</strong></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success"><i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> HIS Gateway Active</span>
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user-circle me-1"></i> Admin User
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/tomvishimpro/public/logout"><i class="fa-solid fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div id="main-content">
        <?php if (isset($view_content)) echo $view_content; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
