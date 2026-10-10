<?php

namespace Tomvis\HIMPro\Core\Exceptions;

use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Logging\Logger;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Throwable;

class ExceptionHandler
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
    }

    public function handleException(Throwable $e): void
    {
        $this->logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ]);

        $request = Request::capture();
        $response = $this->render($request, $e);
        $response->send();
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            $this->logger->error("PHP Error: {$message} in {$file}:{$line}");
        }
        return true;
    }

    public function render(Request $request, Throwable $e): Response
    {
        $debug = Config::get('app.debug', false);
        $code = ($e->getCode() >= 400 && $e->getCode() <= 599) ? $e->getCode() : 500;

        $message = $debug ? $e->getMessage() : 'An internal server error occurred.';
        $details = $debug ? [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ] : null;

        if ($request->isJson()) {
            return JsonResponse::error($message, $code, $details);
        }

        $html = "
        <!DOCTYPE html>
        <html lang='th'>
        <head>
            <meta charset='UTF-8'>
            <title>System Error - TomvisHIMPro</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }</style>
        </head>
        <body>
            <div class='container py-5'>
                <div class='card border-danger shadow-sm'>
                    <div class='card-header bg-danger text-white'>
                        <h4 class='mb-0'>System Error ({$code})</h4>
                    </div>
                    <div class='card-body'>
                        <p class='lead'>{$message}</p>";

        if ($debug && $details) {
            $html .= "<hr><h5>Debug Details:</h5>
            <p><strong>Exception:</strong> {$details['exception']}</p>
            <p><strong>Location:</strong> {$details['file']} (Line {$details['line']})</p>
            <pre class='bg-dark text-light p-3 rounded'><code>" . htmlspecialchars($e->getTraceAsString()) . "</code></pre>";
        }

        $html .= "
                        <a href='/tomvishimpro/public/' class='btn btn-outline-primary mt-3'>Return to Dashboard</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        return new Response($html, $code);
    }
}
