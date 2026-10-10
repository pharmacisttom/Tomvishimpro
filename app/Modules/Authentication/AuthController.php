<?php

namespace Tomvis\HIMPro\Modules\Authentication;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Response\RedirectResponse;
use Tomvis\HIMPro\Core\Security\SessionManager;
use Tomvis\HIMPro\Core\Security\RateLimiter;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;

class AuthController extends BaseController
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function showLogin(Request $request): Response
    {
        return $this->view('auth.login');
    }

    public function login(Request $request): Response|JsonResponse
    {
        $username = trim((string)$request->input('username', ''));
        $password = (string)$request->input('password', '');

        $rateKey = 'login_' . md5($username . '_' . ($_SERVER['REMOTE_ADDR'] ?? ''));

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $msg = 'Account locked due to too many failed login attempts. Try again in 15 minutes.';
            if ($request->isJson()) {
                return $this->jsonError($msg, 429);
            }
            return $this->view('auth.login', ['error' => $msg]);
        }

        $pdo = $this->dbManager->connection();
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name, r.display_name as role_display FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            RateLimiter::hit($rateKey, 900);
            $msg = 'Invalid username or password.';
            if ($request->isJson()) {
                return $this->jsonError($msg, 401);
            }
            return $this->view('auth.login', ['error' => $msg]);
        }

        if ($user['status'] !== 'active') {
            $msg = 'Account is disabled or locked.';
            if ($request->isJson()) {
                return $this->jsonError($msg, 403);
            }
            return $this->view('auth.login', ['error' => $msg]);
        }

        RateLimiter::clear($rateKey);
        SessionManager::regenerate();

        // Load permissions
        $stmtPerm = $pdo->prepare("SELECT p.name FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id JOIN user_roles ur ON rp.role_id = ur.role_id WHERE ur.user_id = ?");
        $stmtPerm->execute([$user['id']]);
        $permissions = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);

        unset($user['password_hash']);
        SessionManager::set('user', $user);
        SessionManager::set('permissions', $permissions);

        // Update last login
        $pdo->prepare("UPDATE users SET last_login_at = NOW(), failed_logins = 0 WHERE id = ?")->execute([$user['id']]);

        // Audit Log
        $pdo->prepare("INSERT INTO audit_logs (user_id, action, module, ip_address, details) VALUES (?, 'Login', 'Authentication', ?, 'User logged in successfully')")
            ->execute([$user['id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

        if ($request->isJson()) {
            return $this->jsonSuccess(['user' => $user], 'Login successful.');
        }

        return new RedirectResponse('/tomvishimpro/public/dashboard');
    }

    public function logout(Request $request): Response
    {
        SessionManager::destroy();
        return new RedirectResponse('/tomvishimpro/public/login');
    }
}
