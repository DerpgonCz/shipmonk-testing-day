<?php

declare(strict_types=1);

namespace App\Http\Request;

class MultiBinPackingRequest
{
    private array $bins = [];

    private array $items = [];

    public function getBins(): array
    {
        return $this->bins;
    }

    public function setBins(array $bins): self
    {
        $this->bins = $bins;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}
