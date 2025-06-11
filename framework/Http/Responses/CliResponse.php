<?php

namespace Framework\Http\Responses;

class CliResponse extends AbstractResponse
{
    /**
     * @var string|array 출력할 내용 (string 또는 lines array)
     */
    protected $content;

    /**
     * @var int CLI에서의 종료 코드 (기본: 0)
     */
    protected int $exitCode = 0;

    /**
     * @param string|array $content
     * @param int $exitCode
     */
    public function __construct($content = '', int $exitCode = 0)
    {
        parent::__construct($content);
        $this->exitCode = $exitCode;
    }

    /**
     * CLI용 출력 구현
     */
    public function send(): void
    {
        // 쿠키/헤더 등 무시(또는 필요하면 로그로 남길 수 있음)
        if (is_array($this->content)) {
            foreach ($this->content as $line) {
                echo $line . PHP_EOL;
            }
        } else {
            echo $this->content . PHP_EOL;
        }
        // PHP 7.4 기준 exit는 값 허용
        exit($this->exitCode);
    }

    /**
     * 종료 코드 설정
     * @return static
     */
    public function setExitCode(int $code)
    {
        $this->exitCode = $code;
        return $this;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
