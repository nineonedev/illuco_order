<?php 

namespace Framework\Database\ORM\Repositories;

use Framework\Database\ORM\Entities\Entity;

class Observer
{
    /**
     * @var array<string, class-string<Obserable>>
     */
    protected array $observers = [];

    /**
     * @param array<string, array<class-string<Obserable>>> $observers
     */
    public function __construct($observers = [])
    {
        $this->observers = $observers;
    }

    /**
     * 이벤트명으로 observer 등록
     * @param string $event  ex) beforeCreate, afterDelete 등
     * @param class-string<Obserable> $observer
     */
    public function register(string $event, $observerClass): void
    {
        $observer = null;
        
        if (is_string($observerClass) && class_exists($observerClass)) {
            $observer = new $observerClass();
        }

        if (!($observer instanceof Obserable)) {
            return; 
        }

        if ($this->hasObserver($event, $observerClass)) return; 

        $this->observers[$event][] = $observer;
    }

    public function hasObserver(string $event, string $observer): bool
    {
        if (empty($this->observers[$event])) return false;
        foreach ($this->observers[$event] as $obs) {
            if (is_a($obs, $observer)) {
                return true; 
            }
        }
        return false;
    }

    /**
     * 배열/클래스명 방식 지원
     * @param string $event
     * @param array<string,array<class-string<Obserable>> $observers
     */
    public function registerMany(string $event, array $observers): void
    {
        foreach ($observers as $obs) {
            $this->register($event, $obs);
        }
    }

    /**
     * 실제 실행
     * @param string $event
     * @param Entity $entity
     */
    public function fire(string $event, Entity $entity): void
    {
        foreach ($this->observers[$event] ?? [] as $observerClass) {
            $observer = new $observerClass();
            $observer->observe($entity, request());
        }
    }
}
