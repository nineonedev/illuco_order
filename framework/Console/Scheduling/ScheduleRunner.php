<?php

namespace Framework\Console\Scheduling;

use DateTime;
use Framework\Console\Output\Output;

class ScheduleRunner
{
    protected Schedule $schedule;
    protected Output $output;

    public function __construct(Schedule $schedule, ?Output $output = null)
    {
        $this->schedule = $schedule;
        $this->output = $output ?: new Output();
    }

    /**
     * 현재 시간 기준으로 실행 가능한 예약 커맨드를 실행합니다.
     */
    public function runDue(): void
    {
        $now = new DateTime();

        foreach ($this->schedule->events() as $event) {
            if ($event->isDue($now)) {
                $this->output->info("[schedule] Running: {$event->getSignature()}");
                $event->run();
            }
        }
    }
}
