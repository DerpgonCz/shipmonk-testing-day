<?php

declare(strict_types=1);

namespace App\DTO;

class PackagingRequest
{
    /**
     * @var \App\DTO\PackagingRequestProduct[]
     */
    private $products = [];

    /**
     * @return \App\DTO\PackagingRequestProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /** @param \App\DTO\PackagingRequestProduct[] $products */
    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }
}
