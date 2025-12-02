<?php
namespace App\Core;

class Response
{

    private array $headers = [];

    public function status(int $code): static
    {
        $this->setStatusCode($code);
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
        $this->headers = [];
    }

    public function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xrw = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($xrw) === 'xmlhttprequest';
    }

    public function json($data, int $status = 200, array $headers = []): void
    {
        $this->status($status)->withHeaders(array_merge(['Content-Type' => 'application/json'], $headers));
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function view(string $name, array $data = []): string
    {
        return App::$app->router->renderView($name, $data);
    }

    public function back(): void
    {
        $target = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($target);
    }

    public function abort(int $code = 404, ?string $message = null): void
    {
        $this->setStatusCode($code);
        if ($this->expectsJson()) {
            $this->json(['error' => $message ?? 'Error', 'status' => $code], $code);
        }

        echo $message ?? 'Error';
        exit;
        // Pindah ke view error nanti, tapi untuk sekarang ini dulu.
    }

    public function download(string $path, ?string $name = null): void
    {
        if (!is_file($path)) {
            $this->abort(404, 'File not found');
        }

        $name = $name ?? basename($path);
        $this->withHeaders([
            'Content-Description' => 'File Transfer',
            'Content-Type' => mime_content_type($path),
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
            'Content-Length' => (string) filesize($path),
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
        ]);
        $this->sendHeaders();
        readfile($path);
        exit;
    }

    public function setStatusCode(int|string $code): void
    {
        http_response_code((int) $code);
    }

    public function getStatusCode(): int
    {
        return http_response_code();
    }

    public function redirect(string $url): void
    {
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

    public function cookie(string $name, string $value, int $expire = 0, string $path = '/'): void
    {
        setcookie($name, $value, [
            'expires' => $expire > 0 ? time() + $expire : $expire,
            'path' => $path,
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}
