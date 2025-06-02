<?php

namespace Framework\Console\Lock;

class LockManager
{
    /**
     * @var array<string, Lock>
     */
    protected array $locks = [];

    /**
     * 커맨드 키에 대한 Lock 인스턴스를 반환합니다.
     * 동일 키에 대해 항상 동일 객체를 반환합니다.
     *
     * @param string $key
     * @return Lock
     */
    public function get(string $key): Lock
    {
        if (!isset($this->locks[$key])) {
            $this->locks[$key] = new Lock($key);
        }

        return $this->locks[$key];
    }

    /**
     * 전체 락을 해제합니다.
     */
    public function releaseAll(): void
    {
        foreach ($this->locks as $lock) {
            $lock->release();
        }

        $this->locks = [];
    }
}
