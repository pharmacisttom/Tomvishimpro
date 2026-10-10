<?php

namespace Tomvis\HIMPro\Core\Middleware;

use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Security\RateLimiter;
use Closure;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decaySeconds = 60): Response
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_' . md5($ip . '_' . $request->getUri());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            if ($request->isJson()) {
                return JsonResponse::error('Too Many Requests. Rate limit exceeded.', 429);
            }
            return new Response('429 Too Many Requests', 429);
        }

        RateLimiter::hit($key, $decaySeconds);
        return $next($request);
    }
}
