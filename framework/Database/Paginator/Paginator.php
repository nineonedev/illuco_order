<?php

namespace Framework\Database\Paginator;

class Paginator
{
    protected array $items;
    protected int $total;
    protected int $perPage;
    protected int $currentPage;

    public function __construct(array $items, int $total, int $perPage, int $currentPage)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function nextPageUrl(): ?string
    {
        return $this->hasMorePages()
            ? '?page=' . ($this->currentPage + 1)
            : null;
    }

    public function previousPageUrl(): ?string
    {
        return $this->currentPage > 1
            ? '?page=' . ($this->currentPage - 1)
            : null;
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

    public function toArray(): array
    {
        return [
            'data' => $this->itemsWithNo(),
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
        ];
    }

    public function itemsWithNo(): array
    {
        $number = $this->total - (($this->currentPage - 1) * $this->perPage);
        $itemsWithNo = [];

        foreach ($this->items as $item) {
            if (is_array($item)) {
                $item['_no'] = $number--;
                $itemsWithNo[] = $item;
            } elseif (is_object($item)) {
                $cloned = clone $item;
                $cloned->_no = $number--;
                $itemsWithNo[] = $cloned;
            } else {
                $itemsWithNo[] = $item;
            }
        }

        return $itemsWithNo;
    }

}
