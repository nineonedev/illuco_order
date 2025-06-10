<?php

namespace Framework\Http;

use Framework\Support\Facades\View;

class Response
{
    protected string $content = '';
    protected int $statusCode = 200;
    protected array $headers = [];

    protected ?string $filePath = null;
    protected bool $isDownload = false;

    protected bool $isStream = false;
    protected int $chunkSize = 8192; // 8KB 기본


    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($statusCode);

        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * @return static
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return static
     */
    public function setStatusCode(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * @return static
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @return static
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this; 
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return static
     */
    public function withCookie(string $name, string $value, int $minutes = 60)
    {
        $expire = time() + ($minutes * 60); 
        setcookie($name, $value, $expire, '/'); 
        return $this; 
    }

    /**
     * @return static
     */
    public function with(string $key, $value)
    {
        $_SESSION[$key] = $value; 
        return $this;
    }

    /**
     * @return static
     */
    public function withErrors(array $errors)
    {
        $_SESSION['_errors'] = $errors; 
        return $this;
    }

    /**
     * @return static
     */
    public function withInput(array $input = [])
    {
        $_SESSION['_old_input'] = $input; 
        return $this;
    }

    /**
     * @return static
     */
    public static function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return (new self('', 302))->setHeader('Location', $referer);
    }


    public function send(): Response
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        if ($this->isDownload && $this->filePath) {
            readfile($this->filePath);
            return $this;
        }

        if ($this->isStream && $this->filePath) {
            $handle = fopen($this->filePath, 'rb');

            if ($handle === false) {
                echo '파일 열기에 실패했습니다.';
                return $this;
            }

            while (!feof($handle)) {
                echo fread($handle, $this->chunkSize);
                flush(); // buffer 강제 flush
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }

            fclose($handle);
            return $this;
        }

        echo $this->content;
        return $this;
    }

    /**
     * @return static
     */
    public static function create(string $content = '', int $statusCode = 200, array $headers = [])
    {
        return new Response($content, $statusCode, $headers);
    }

    public static function redirect(string $url, int $status = 302): Response
    {
        return (new self('', $status))->setHeader('Location', $url);
    }

    public static function view(string $template, array $data = [], int $statusCode = 200): Response
    {
        $content = View::render($template, $data); 
        return new self($content, $statusCode, ['Content-Type' =>'text/html; charset=utf8']);
    }

    /**
     * @return static
     */
    public static function json(array $data, int $statusCode = 200)
    {
        return new self(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @param string $filePath 실제 파일 경로 (예: /var/www/storage/files/report.pdf)
     * @param string|null $downloadName 사용자에게 보여질 파일명 (null이면 basename 자동 추출)
     */
    public static function download(string $filePath, ?string $downloadName = null): Response
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return new self('파일을 찾을 수 없습니다.', 404, ['Content-Type' => 'text/plain']);
        }

        $downloadName = $downloadName ?? basename($filePath);
        $fileSize     = filesize($filePath);
        $mimeType     = mime_content_type($filePath) ?: 'application/octet-stream';

        // 헤더 설정
        $headers = [
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $mimeType,
            'Content-Disposition'       => 'attachment; filename="' . rawurlencode($downloadName) . '"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => '0',
            'Cache-Control'             => 'must-revalidate',
            'Pragma'                    => 'public',
            'Content-Length'            => (string) $fileSize,
        ];

        // 응답 객체 생성 (본문은 비우고, send()에서 직접 출력)
        $response = new self('', 200, $headers);

        // 커스텀 처리 플래그 (send 시 파일을 읽어 전송)
        $response->filePath = $filePath;
        $response->isDownload = true;

        return $response;
    }

    /**
     * 스트리밍 방식으로 대용량 파일 다운로드
     *
     * @param string $filePath
     * @param string|null $downloadName
     * @param int $chunkSize 청크 크기 (바이트)
     * @return static
     */
    public static function streamDownload(string $filePath, ?string $downloadName = null, int $chunkSize = 8192): Response
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return new self('파일을 찾을 수 없습니다.', 404, ['Content-Type' => 'text/plain']);
        }

        $downloadName = $downloadName ?? basename($filePath);
        $fileSize     = filesize($filePath);
        $mimeType     = mime_content_type($filePath) ?: 'application/octet-stream';

        $headers = [
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $mimeType,
            'Content-Disposition'       => 'attachment; filename="' . rawurlencode($downloadName) . '"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => '0',
            'Cache-Control'             => 'must-revalidate',
            'Pragma'                    => 'public',
            'Content-Length'            => (string) $fileSize,
        ];

        $response = new self('', 200, $headers);
        $response->filePath = $filePath;
        $response->isStream = true;
        $response->chunkSize = $chunkSize;

        return $response;
    }

}
