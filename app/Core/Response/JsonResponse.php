<?php

namespace Tomvis\HIMPro\Core\Response;

class JsonResponse extends Response
{
    public function __construct(mixed $data = [], int $statusCode = 200, array $headers = [])
    {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $headers['Content-Type'] = 'application/json; charset=UTF-8';
        parent::__construct($content, $statusCode, $headers);
    }

    public static function success(mixed $data = [], string $message = 'Success', int $code = 200): static
    {
        return new static([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s'),
        ], $code);
    }

    public static function error(string $message = 'Error', int $code = 400, mixed $errors = null): static
    {
        return new static([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s'),
        ], $code);
    }
}
