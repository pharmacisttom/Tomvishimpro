<?php

namespace Tomvis\HIMPro\Core\Exceptions;

use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Logging\Logger;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Throwable;

class ErrorCaptureHandler
{
    protected Logger $logger;
    protected ErrorIngestionService $ingestionService;
    protected string $traceId;

    public function __construct(?Logger $logger = null, ?ErrorIngestionService $ingestionService = null)
    {
        $this->logger = $logger ?: new Logger();
        $this->ingestionService = $ingestionService ?: new ErrorIngestionService();
        $this->traceId = $this->generateTraceId();
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleException(Throwable $e): void
    {
        $sanitizedMsg = ErrorSanitizer::sanitizeText($e->getMessage());
        $sanitizedTrace = ErrorSanitizer::sanitizeText($e->getTraceAsString());

        $this->logger->error("{$sanitizedMsg} [Trace ID: {$this->traceId}]", [
            'file' => ErrorSanitizer::sanitizeText($e->getFile()),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ]);

        // Ingest into Central Error Intelligence non-blockingly
        $this->ingestionService->ingest([
            'exception_class' => get_class($e),
            'normalized_code' => (string)$e->getCode(),
            'source_component' => $e->getFile(),
            'sanitized_message' => $sanitizedMsg,
            'sanitized_trace' => $sanitizedTrace,
            'trace_id' => $this->traceId,
            'app_id' => Config::get('app.name', 'TomvisHIMPro'),
            'module_id' => 'Core',
            'environment' => Config::get('app.env', 'local'),
            'severity' => $this->determineSeverity($e),
        ]);

        $request = Request::capture();
        $response = $this->render($request, $e);
        $response->send();
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        $sanitizedMsg = ErrorSanitizer::sanitizeText($message);
        $sanitizedFile = ErrorSanitizer::sanitizeText($file);

        $this->logger->error("PHP Error [{$level}]: {$sanitizedMsg} in {$sanitizedFile}:{$line} [Trace ID: {$this->traceId}]");

        $this->ingestionService->ingest([
            'exception_class' => 'PHPError',
            'normalized_code' => (string)$level,
            'source_component' => $sanitizedFile,
            'sanitized_message' => $sanitizedMsg,
            'sanitized_trace' => "Error level {$level} at {$sanitizedFile}:{$line}",
            'trace_id' => $this->traceId,
            'app_id' => Config::get('app.name', 'TomvisHIMPro'),
            'module_id' => 'Core',
            'environment' => Config::get('app.env', 'local'),
            'severity' => ($level === E_USER_ERROR || $level === E_RECOVERABLE_ERROR) ? 'High' : 'Medium',
        ]);

        return true;
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $sanitizedMsg = ErrorSanitizer::sanitizeText($error['message']);
            $sanitizedFile = ErrorSanitizer::sanitizeText($error['file']);

            $this->ingestionService->ingest([
                'exception_class' => 'FatalError',
                'normalized_code' => (string)$error['type'],
                'source_component' => $sanitizedFile,
                'sanitized_message' => $sanitizedMsg,
                'sanitized_trace' => "Fatal error at {$sanitizedFile}:{$error['line']}",
                'trace_id' => $this->traceId,
                'app_id' => Config::get('app.name', 'TomvisHIMPro'),
                'module_id' => 'Core',
                'environment' => Config::get('app.env', 'local'),
                'severity' => 'Critical',
            ]);
        }
    }

    public function render(Request $request, Throwable $e): Response
    {
        $debug = Config::get('app.debug', false);
        $code = ($e->getCode() >= 400 && $e->getCode() <= 599) ? $e->getCode() : 500;

        $message = $debug ? ErrorSanitizer::sanitizeText($e->getMessage()) : 'An internal server error occurred.';
        $details = $debug ? [
            'exception' => get_class($e),
            'file' => ErrorSanitizer::sanitizeText($e->getFile()),
            'line' => $e->getLine(),
            'trace_id' => $this->traceId,
            'trace' => explode("\n", ErrorSanitizer::sanitizeText($e->getTraceAsString())),
        ] : ['trace_id' => $this->traceId];

        if ($request->isJson()) {
            return JsonResponse::error($message, $code, $details);
        }

        $traceIdHtml = htmlspecialchars($this->traceId);
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
                    <div class='card-header bg-danger text-white d-flex justify-content-between align-items-center'>
                        <h4 class='mb-0'>System Error ({$code})</h4>
                        <span class='badge bg-light text-dark'>Trace ID: {$traceIdHtml}</span>
                    </div>
                    <div class='card-body'>
                        <p class='lead'>" . htmlspecialchars($message) . "</p>";

        if ($debug) {
            $html .= "<hr><h5>Debug Details:</h5>
            <p><strong>Exception:</strong> " . htmlspecialchars(get_class($e)) . "</p>
            <p><strong>Location:</strong> " . htmlspecialchars(ErrorSanitizer::sanitizeText($e->getFile())) . " (Line {$e->getLine()})</p>
            <pre class='bg-dark text-light p-3 rounded'><code>" . htmlspecialchars(ErrorSanitizer::sanitizeText($e->getTraceAsString())) . "</code></pre>";
        }

        $html .= "
                        <a href='?route=/dashboard' class='btn btn-outline-primary mt-3'>Return to Dashboard</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        return new Response($html, $code);
    }

    protected function determineSeverity(Throwable $e): string
    {
        if (str_contains(get_class($e), 'PDOException') || str_contains($e->getMessage(), 'CRITICAL')) {
            return 'Critical';
        }
        if (str_contains(get_class($e), 'AuthException') || str_contains(get_class($e), 'HISGatewayException')) {
            return 'High';
        }
        return 'Medium';
    }

    protected function generateTraceId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
