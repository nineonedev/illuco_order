<?php

namespace Framework\Http;

class Resource
{
    protected $data;

    protected array $additional = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function make($data): self
    {
        return new static($data);
    }

    public static function collection(array $items): array
    {
        return array_map(fn($item) => (new static($item))->resolve(), $items);
    }

    public function resolve(): array
    {
        return array_merge($this->transform($this->data), $this->additional);
    }

    protected function transform($data): array
    {
        if (is_array($data)) return $data;
        if (method_exists($data, 'toArray')) return $data->toArray();
        return (array)$data;
    }

    public function toResponse(int $status = 200): Response
    {
        return Response::json($this->resolve(), $status);
    }

    public function additional(array $meta): self
    {
        $this->additional = ['meta' => $meta];
        return $this;
    }
}
