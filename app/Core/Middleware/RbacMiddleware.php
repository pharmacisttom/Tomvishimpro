<?php

namespace Tomvis\HIMPro\Core\Middleware;

use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Security\SessionManager;
use Closure;

class RbacMiddleware
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        SessionManager::start();
        $user = SessionManager::get('user');

        if (!$user) {
            if ($request->isJson()) {
                return JsonResponse::error('Unauthorized access.', 401);
            }
            return new Response('401 Unauthorized', 401);
        }

        // Super Admin bypasses all checks
        if (($user['role_name'] ?? '') === 'super_admin') {
            return $next($request);
        }

        $userPermissions = SessionManager::get('permissions', []);

        if ($permission && !in_array($permission, $userPermissions)) {
            if ($request->isJson()) {
                return JsonResponse::error("Forbidden. Missing required permission: [{$permission}]", 403);
            }
            return new Response("403 Forbidden - Missing permission: {$permission}", 403);
        }

        return $next($request);
    }
}
