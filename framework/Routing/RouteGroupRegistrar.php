<?php 

namespace Framework\Routing;

use Closure;
use Framework\Http\Contracts\MiddlewareInterface;

class RouteGroupRegistrar
{
    protected array $groupStack = []; 
    protected array $attributes = []; 
    protected RouteRegistrar $registrar;
    

    public function __construct(RouteRegistrar $registrar)
    {
        $this->registrar = $registrar; 
    }

    public function prefix(string $prefix): self 
    {
        $this->attributes['prefix'] = $prefix; 
        return $this; 
    }

    public function name(string $name): self
    {
        $this->attributes['as'] = $name;
        return $this;
    }

    /**
     * @param class-string<MiddlewareInterface>|array<int,class-string<MiddlewareInterface>>$middleware
     */
    public function middleware($middleware): self
    {
        $this->attributes['middleware'] = array_merge(
            $this->middleware ?? [],
            (array) $middleware
        );

        return $this; 
    }

    public function controller(string $controller): self
    {
        $this->attributes['controller'] = $controller; 
        return $this; 
    }

    public function group(Closure $callback): void
    {
        $this->pushGroup($this->attributes);
        $this->attributes = [];

        $merged = $this->mergeGroupAttributes();

        $previous = $this->registrar->getGroupAttributes(); 

        $this->registrar->setGroupAttributes($merged); 

        $callback($this->registrar);
        
        $this->registrar->setGroupAttributes($previous); 
        $this->popGroup();
    }

    protected function pushGroup(array $attributes): void
    {
        $this->groupStack[] = $attributes; 
    }

    protected function popGroup(): void
    {
        array_pop($this->groupStack); 
    }

    protected function mergeGroupAttributes(): array
    {
        $result = [
            'prefix' => '',
            'middleware' => [], 
            'as' => '',
            'controller' => null,
        ];

        foreach ($this->groupStack as $group) {
            if (!empty($group['prefix'])) {
                $result['prefix'] = rtrim($result['prefix'], '/') . '/' . ltrim($group['prefix'], '/');
            }

            if (!empty($group['middleware'])) {
                $result['middleware'] = array_merge($result['middleware'], $group['middleware']); 
            }

            if (!empty($group['as'])) {
                $result['as'] .= $group['as']; 
            }

            if (!empty($group['controller'])) {
                $result['controller'] = $group['controller']; 
            }
        }

        $result['middleware'] = array_unique($result['middleware']); 
        $result['prefix'] = '/' . trim($result['prefix'], '/'); 

        return $result; 
    }
}