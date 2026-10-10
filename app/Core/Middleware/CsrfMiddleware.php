<?php

namespace Tomvis\HIMPro\Core\Middleware;

use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Security\CsrfProtection;
use Closure;

class CsrfMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!CsrfProtection::validate($request)) {
                if ($request->isJson()) {
                    return JsonResponse::error('CSRF Token Mismatch / Invalid Security Token', 419);
                }
                return new Response('419 Page Expired (CSRF Token Mismatch)', 419);
            }
        }
        return $next($request);
    }
}
