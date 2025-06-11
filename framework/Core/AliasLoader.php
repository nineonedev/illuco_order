<?php 

namespace Framework\Core;

class AliasLoader
{
    protected Container $container;
    protected array $aliases = []; 
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setAliases(array $aliases): void
    {
        foreach ($aliases as $alias => $class) {
            $this->set($alias, $class); 
        }
    }

    public function set(string $alias, string $class): void
    {
        // 별칭은 문자열, 클래스는 \ 포함된 FQCN이어야 함
        if (!is_string($alias) || !is_string($class)) {
            throw new \InvalidArgumentException("별칭/클래스는 반드시 문자열이어야 합니다. ['{$alias}' => '{$class}']");
        }
        if (strpos($class, '\\') === false) {
            throw new \InvalidArgumentException("클래스 이름 '{$class}'는 FQCN(네임스페이스 포함)이어야 합니다.");
        }
        // 별칭에 역슬래시 있으면 에러 (별칭이 클래스처럼 등록되는 실수 방지)
        if (strpos($alias, '\\') !== false) {
            throw new \InvalidArgumentException("별칭 '{$alias}'는 짧은 이름이어야 하며 네임스페이스를 포함하면 안 됩니다.");
        }

        $this->aliases[$alias] = $class;
    }
    public function get(string $alias): ?string
    {
        return $this->aliases[$alias] ?? $alias; 
    }

    public function all(): array
    {
        return $this->aliases; 
    }

    public function has(string $alias): bool
    {
        return array_key_exists($alias, $this->aliases); 
    }

    public function register(): void
    {
        foreach ($this->aliases as $alias => $class) {
            if (!class_exists($alias) && class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }
}