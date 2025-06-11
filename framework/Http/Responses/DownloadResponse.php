<?php

namespace Framework\Http\Responses;

class DownloadResponse extends AbstractResponse
{
    /** @var string */
    protected string $filePath;

    /** @var string|null */
    protected ?string $downloadName = null;

    /** @var bool */
    protected bool $useStream = false;

    /** @var int */
    protected int $chunkSize = 8192;

    /**
     * DownloadResponse constructor.
     * @param string $filePath
     * @param string|null $downloadName
     * @param bool $useStream
     * @param int $chunkSize
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(
        string $filePath,
        ?string $downloadName = null,
        bool $useStream = false,
        int $chunkSize = 8192,
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->filePath     = $filePath;
        $this->downloadName = $downloadName ?? basename($filePath);
        $this->useStream    = $useStream;
        $this->chunkSize    = $chunkSize;

        $mimeType = is_file($filePath) ? mime_content_type($filePath) : 'application/octet-stream';
        $fileSize = is_file($filePath) ? filesize($filePath) : 0;

        $defaultHeaders = [
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $mimeType,
            'Content-Disposition'       => 'attachment; filename="' . rawurlencode($this->downloadName) . '"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => '0',
            'Cache-Control'             => 'must-revalidate',
            'Pragma'                    => 'public',
            'Content-Length'            => (string) $fileSize,
        ];
        // 전달된 headers가 우선적으로 덮어쓰도록 (사용자 커스텀 지원)
        $headers = array_merge($defaultHeaders, $headers);

        parent::__construct('', $statusCode, $headers);
    }

    /**
     * 스트림 모드 설정 (체이닝)
     * @param int $chunkSize
     * @return $this
     */
    public function asStream(int $chunkSize = 8192): self
    {
        $this->useStream = true;
        $this->chunkSize = $chunkSize;
        return $this;
    }

    /**
     * 파일 응답 전송
     */
    public function send(): void
    {
        $this->sendHeaders();

        if (!is_file($this->filePath) || !is_readable($this->filePath)) {
            // 실제 서비스에선 에러핸들러 호출 가능
            echo '파일을 읽을 수 없습니다.';
            return;
        }

        // 버퍼 비우기 (대용량 파일을 위해)
        if (ob_get_length()) {
            ob_end_clean();
        }

        if ($this->useStream) {
            $handle = fopen($this->filePath, 'rb');
            if ($handle === false) {
                echo '파일 열기에 실패했습니다.';
                return;
            }
            while (!feof($handle)) {
                echo fread($handle, $this->chunkSize);
                flush();
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
            fclose($handle);
        } else {
            // 단일 파일 전체 전송
            readfile($this->filePath);
        }
    }

    /**
     * 다운로드 파일명 지정자 (optional, builder)
     * @return string|null
     */
    public function getDownloadName(): ?string
    {
        return $this->downloadName;
    }
}
