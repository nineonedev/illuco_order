<?php

namespace Framework\Bus;

use Framework\Bus\Contracts\JobInterface;

class Worker
{
    protected QueueManager $queue; 

    public function __construct(QueueManager $queue)
    {
        $this->queue = $queue; 
    }

    public function run(string $queue = 'default', int $maxJobs = 0, int $sleepSeconds = 1): void
    {
        $jobsRun = 0; 

        while (true) {
            $job = $this->queue->pop($queue); 

            if ($job instanceof JobInterface) {
                try {
                    $job->handle();
                    $jobsRun++; 
                } catch (\Throwable $e) {
                    echo "[Worker] Job 실행 중 오류: ". $e->getMessage(). "\n"; 
                }
            } else {
                sleep($sleepSeconds); 
            }

            if ($maxJobs > 0 && $jobsRun >= $maxJobs) {
                break; 
            }
        }
    }
}