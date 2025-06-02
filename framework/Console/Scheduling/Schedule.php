<?php

namespace Framework\Console\Scheduling;

use Closure;

class Schedule
{
    /**
     * @var ScheduledCommand[]
     */
    protected array $events = [];

    /**
     * 스케줄링할 커맨드를 등록합니다.
     *
     * @param string $signature
     * @param Closure $callback
     * @return ScheduledCommand
     */
    public function command(string $signature, Closure $callback): ScheduledCommand
    {
        $event = new ScheduledCommand($signature, $callback);
        $this->events[] = $event;

        return $event;
    }

    /**
     * 등록된 예약 커맨드 목록을 반환합니다.
     *
     * @return ScheduledCommand[]
     */
    public function events(): array
    {
        return $this->events;
    }

    /**
     * 현재 등록된 커맨드 개수 반환
     */
    public function count(): int
    {
        return count($this->events);
    }

    /**
     * 현재 등록된 모든 시그니처 리스트 반환
     *
     * @return string[]
     */
    public function signatures(): array
    {
        return array_map(function (ScheduledCommand $event) {
            return $event->getSignature();
        }, $this->events);
    }

    /**
     * 이벤트를 모두 제거합니다.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->events = [];
    }
}
