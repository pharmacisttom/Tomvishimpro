<?php

namespace Tomvis\HIMPro\Core\Middleware;

use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Response\RedirectResponse;
use Tomvis\HIMPro\Core\Security\SessionManager;
use Closure;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        SessionManager::start();
        $user = SessionManager::get('user');

        if (!$user) {
            if ($request->isJson()) {
                return JsonResponse::error('Unauthorized. Authentication required.', 401);
            }
            return new RedirectResponse('/tomvishimpro/public/login');
        }

        return $next($request);
    }
}
