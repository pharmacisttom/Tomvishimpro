<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - TomvisHIMPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #0056b3;
            color: #ffffff;
            padding: 2rem 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-hospital-user fa-3x mb-2"></i>
            <h3 class="fw-bold mb-0">TomvisHIMPro</h3>
            <p class="mb-0 text-white-50">Enterprise Hospital Framework</p>
        </div>
        <div class="p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="/tomvishimpro/public/login" method="POST">
                <?= $csrf_field ?>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">ชื่อผู้ใช้งาน (Username)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="admin" value="admin" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Admin123456!" value="Admin123456!" required>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ
                    </button>
                </div>
            </form>
            <div class="mt-4 text-center text-muted small">
                <p class="mb-1">Default Account: <code>admin</code> / <code>Admin123456!</code></p>
                <span>TomvisHIMPro Healthcare Platform v1.0</span>
            </div>
        </div>
    </div>
</body>
</html>
