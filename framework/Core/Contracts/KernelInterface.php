<?php

namespace Framework\Core\Contracts;

use Framework\Console\Input;
use Framework\Http\Request;

interface KernelInterface
{
    /**
     * 주어진 입력을 처리합니다.
     * 
     * @param Input|Request $input
     * @return mixed
     */
    public function handle($input);

    /**
     * 요청 처리 후 종료 작업을 수행합니다.
     *
     * @param mixed $input
     * @param mixed|null $result
     * @return void
     */
    public function terminate($input, $result = null): void;
}
