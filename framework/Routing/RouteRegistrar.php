<?php

namespace Framework\Routing;

class RouteRegistrar
{
    protected RouteCollection $collection;
    protected array $groupAttributes = [];

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function setGroupAttributes(array $attributes): void
    {
        $this->groupAttributes = $attributes; 
    }

    public function getGroupAttributes(): array
    {
        return $this->groupAttributes; 
    }

    public function clearGroupAttributes(): void
    {
        $this->groupAttributes = [];
    }

    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function any(string $uri, $action): Route
    {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $this->addRoute($method, $uri, $action);
        }

        return $this->addRoute('GET', $uri, $action);
    }
    

    protected function addRoute(string $method, string $uri, $action): Route
    {
        $attributes = $this->groupAttributes; 

        $prefix = $attributes['prefix'] ?? '';
        $fullUri = $prefix . '/' . trim($uri, '/');
        $fullUri = '/' . trim($fullUri, '/');

        $action = $this->buildAction($action, $attributes['controller'] ?? null);  
        $route = new Route($method, $fullUri, $action);
        
        if ($route instanceof Route) {
            $route->setRouteCollection($this->collection); 
        }

        if (!empty($attributes['middleware'])) {
            $route->middleware($attributes['middleware']);
        }

        if (!empty($attributes['as'])) {
            $route->setNamePrefix($attributes['as']); 
        }

        $this->collection->add($route);

        return $route;
    }

    protected function buildAction($action, $controller = null)
    {
        if (is_string($action) && $controller) {
            return [$controller, $action]; 
        }

        return $action; 
    }

    public function resource(string $name, string $controller, array $options = []): void
    {
        $only = $options['only'] ?? null;
        $except = $options['except'] ?? [];

        $shouldRegister = function (string $action) use ($only, $except): bool {
            if ($only !== null && !in_array($action, $only, true)) return false;
            if (in_array($action, $except, true)) return false;
            return true;
        };

        if ($shouldRegister('index')) {
            $this->get("/{$name}", [$controller, 'index'])->name("{$name}.index");
        }

        if ($shouldRegister('create')) {
            $this->get("/{$name}/create", [$controller, 'create'])->name("{$name}.create");
        }

        if ($shouldRegister('store')) {
            $this->post("/{$name}", [$controller, 'store'])->name("{$name}.store");
        }

        if ($shouldRegister('show')) {
            $this->get("/{$name}/{id?}", [$controller, 'show'])->name("{$name}.show");
        }

        if ($shouldRegister('edit')) {
            $this->get("/{$name}/{id?}/edit", [$controller, 'edit'])->name("{$name}.edit");
        }

        if ($shouldRegister('update')) {
            $this->put("/{$name}/{id}", [$controller, 'update'])->name("{$name}.update");
            $this->patch("/{$name}/{id}", [$controller, 'update'])->name("{$name}.update");
        }

        if ($shouldRegister('destroy')) {
            $this->delete("/{$name}/{id}", [$controller, 'destroy'])->name("{$name}.destroy");
        }
    }
}
