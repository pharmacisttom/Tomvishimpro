<?php

namespace Tomvis\HIMPro\Core\Security;

use Tomvis\HIMPro\Core\Request\Request;

class CsrfProtection
{
    public static function getToken(): string
    {
        SessionManager::start();
        $token = SessionManager::get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            SessionManager::set('_csrf_token', $token);
        }
        return $token;
    }

    public static function validate(Request $request): bool
    {
        $sessionToken = SessionManager::get('_csrf_token');
        if (!$sessionToken) {
            return false;
        }

        $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

        if (!$requestToken || !is_string($requestToken)) {
            return false;
        }

        return hash_equals($sessionToken, $requestToken);
    }

    public static function field(): string
    {
        $token = self::getToken();
        return sprintf('<input type="hidden" name="_token" value="%s">', htmlspecialchars($token, ENT_QUOTES, 'UTF-8'));
    }
}
