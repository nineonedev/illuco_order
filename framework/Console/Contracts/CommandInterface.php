<?php

namespace Framework\Console\Contracts;

use Framework\Console\Input\Input;
use Framework\Console\Output\Output;

interface CommandInterface
{
    /**
     * 이 커맨드를 호출할 때 사용할 고유한 시그니처를 반환합니다.
     *
     * 예: 'hello', 'user:create', 'cache:clear'
     *
     * @return string
     */
    public function signature(): string;

    /**
     * 커맨드의 간략한 설명을 반환합니다.
     *
     * @return string
     */
    public function description(): string;

    /**
     * 커맨드 실행 본문입니다.
     *
     * @param Input $input
     * @param Output $output
     * @return void
     */
    public function execute(Input $input, Output $output): void;
}
