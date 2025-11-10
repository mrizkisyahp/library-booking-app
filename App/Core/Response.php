<?php
namespace App\Core;

class Response {

    public function setStatusCode(int|string $code): void {
        http_response_code((int)$code);
    }

    public function getStatusCode(): int {
        return http_response_code();
    }

    public function redirect(string $url): void {
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Location: ' . $url, true, 302);
        flush();
        exit;
    }

    public function resolveTitle(?Controller $controller): string
    {
        if ($controller && method_exists($controller, 'getTitle')) {
            $title = $controller->getTitle();
            if (!empty($title)) {
                return $title;
            }
        }

        return match ($this->getStatusCode()) {
            403 => '403 Forbidden | Library Booking App',
            404 => '404 Not Found | Library Booking App',
            default => 'Library Booking App',
        };
    }
}
