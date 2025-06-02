<?php

namespace Framework\Bus;

use Framework\Bus\Contracts\JobInterface;

class QueueManager
{
    /**
     * @var array<string,array<int,array{job:JobInterface,ready_at:int}>>
     */
    protected array $queues = [];

    public function push(JobInterface $job): void
    {
        $queue = $job->queue(); 

        if (!isset($this->queues[$queue])) {
            $this->queues[$queue] = []; 
        }

        $this->queues[$queue][] = [
            'job' => $job,
            'ready_at' => time() + $job->delay(),
        ];
    }

    public function pop(string $queue = 'default'): ?JobInterface
    {
        if (!isset($this->queues[$queue])) {
            return null;
        }

        foreach ($this->queues[$queue] as $index => $entry) {
            if (time() >= $entry['ready_at']) {
                unset($this->queues[$queue][$index]); 
                return $entry['job']; 
            }
        }

        return null; 
    }

    public function hasPending(string $queue = 'default'): bool
    {
        return !empty($this->queues[$queue]); 
    }
}