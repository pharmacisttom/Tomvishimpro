<?php

namespace Tomvis\HIMPro\Core\Controller;

use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Security\CsrfProtection;
use Exception;

abstract class BaseController
{
    protected function json(mixed $data, int $code = 200): JsonResponse
    {
        return new JsonResponse($data, $code);
    }

    protected function jsonSuccess(mixed $data = [], string $message = 'Success', int $code = 200): JsonResponse
    {
        return JsonResponse::success($data, $message, $code);
    }

    protected function jsonError(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
    {
        return JsonResponse::error($message, $code, $errors);
    }

    protected function view(string $viewPath, array $data = []): Response
    {
        $baseViewDir = 'd:/xampp/htdocs/tomvishimpro/resources/views/';
        $filePath = $baseViewDir . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($filePath)) {
            throw new Exception("View [{$viewPath}] not found at {$filePath}");
        }

        extract($data);
        $csrf_field = CsrfProtection::field();
        $csrf_token = CsrfProtection::getToken();

        ob_start();
        include $filePath;
        $content = ob_get_clean();

        return new Response($content);
    }
}
