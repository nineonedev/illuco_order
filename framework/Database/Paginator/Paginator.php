<?php

namespace Framework\Database\Paginator;

use Framework\Database\ORM\Entities\Entity;
use Framework\Http\ApiResource;
use Framework\Support\Collection;

class Paginator
{
    const ROW_NUMBER_KEY = '__ROW_NO__';

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

        $this->addNumberingToEntities();
    }

    protected function addNumberingToEntities(): void
    {
        $number = $this->total - (($this->currentPage - 1) * $this->perPage);

        foreach ($this->items as $item) {
            if ($item instanceof Entity) {
                $item->setMeta(self::ROW_NUMBER_KEY, $number--);
            }
        }
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

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    public function nextPageUrl(): ?string
    {
        return $this->hasNextPage() ? '?page=' . ($this->currentPage + 1) : null;
    }

    public function previousPageUrl(): ?string
    {
        return $this->hasPreviousPage() ? '?page=' . ($this->currentPage - 1) : null;
    }

    public function from(): int
    {
        return ($this->total === 0) ? 0 : (($this->currentPage - 1) * $this->perPage + 1);
    }

    public function to(): int
    {
        return min($this->from() + $this->items->count() - 1, $this->total);
    }

    
    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn($item) => $item instanceof Entity ? $item->toArray() : $item,
                $this->items->all()
            ),
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
            'has_next_page' => $this->hasNextPage(),
            'has_previous_page' => $this->hasPreviousPage(),
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
    
    /**
     * @param class-string<ApiResource> $apiResource
     */
    public function toResource(string $apiResource): array
    {
        $data = array_map(function ($item) use ($apiResource) {
            /** @var ApiResource $resource */
            $resource = new $apiResource($item);
            return $resource->toArray();
        }, $this->items->all());

        return [
            'data' => $data,
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
        ];
    }

}
