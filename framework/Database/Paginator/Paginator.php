<?php

namespace Framework\Database\Paginator;

use Framework\Support\Collection;

class Paginator
{
    protected Collection $items;
    protected int $total;
    protected int $perPage;
    protected int $currentPage;

    public function __construct($items, int $total, int $perPage, int $currentPage)
    {
        if (is_array($items)) {
            $this->items = new Collection($items);
        } elseif ($items instanceof Collection) {
            $this->items = $items;
        } else {
            throw new \InvalidArgumentException('Items must be an array or instance of Collection');
        }

        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    public function nextPageUrl(): ?string
    {
        return $this->hasMorePages() ? '?page=' . ($this->currentPage + 1) : null;
    }

    public function previousPageUrl(): ?string
    {
        return $this->currentPage > 1 ? '?page=' . ($this->currentPage - 1) : null;
    }

    public function from(): int
    {
        return ($this->total === 0) ? 0 : (($this->currentPage - 1) * $this->perPage + 1);
    }

    public function to(): int
    {
        return min($this->from() + $this->items->count() - 1, $this->total);
    }

    public function itemsWithNo(): array
    {
        $number = $this->total - (($this->currentPage - 1) * $this->perPage);
        $results = [];

        foreach ($this->items->all() as $item) {
            if (is_array($item)) {
                $item['_no'] = $number--;
                $results[] = $item;
            } elseif (is_object($item)) {
                if (method_exists($item, 'toArray')) {
                    $data = $item->toArray();
                    $data['_no'] = $number--;
                    $results[] = $data;
                } else {
                    $item->_no = $number--;
                    $results[] = $item;
                }
            }
        }

        return $results;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->itemsWithNo(),
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'from' => $this->from(),
            'to' => $this->to(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function apiResource(?callable $transformer = null): array
    {
        return [
            'data' => array_map(function ($item) use ($transformer) {
                if ($transformer) {
                    return $transformer($item);
                }

                if (is_object($item) && method_exists($item, 'toArray')) {
                    return $item->toArray();
                }

                return $item;
            }, $this->items()),
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
        ];
    }

    public function withNoResource(?callable $transformer = null): array
    {
        $number = $this->total - (($this->currentPage - 1) * $this->perPage);
        $results = [];

        foreach ($this->items->all() as $item) {
            if ($transformer) {
                $transformed = $transformer($item);
            } elseif (is_object($item) && method_exists($item, 'toArray')) {
                $transformed = $item->toArray();
            } else {
                $transformed = $item;
            }

            $transformed['_no'] = $number--;
            $results[] = $transformed;
        }

        return [
            'data' => $results,
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'from' => $this->from(),
            'to' => $this->to(),
        ];
    }


}
