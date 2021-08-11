<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\PackagingRequest;
use App\DTO\PackagingRequestProduct;
use App\Entity\Packaging;
use App\Http\Request\MultiBinPackingRequest;

class PackagingRequestFactory
{
    public function createMultiBinPackingRequest(
        PackagingRequest $packagingRequest,
        array $bins = []
    ): MultiBinPackingRequest {
        return (new MultiBinPackingRequest())
            ->setBins(
                array_map(function (Packaging $bin): array {
                    return $this->buildBind($bin);
                }, $bins)
            )
            ->setItems(
                array_map(function (PackagingRequestProduct $product): array {
                    return $this->buildItem($product);
                }, $packagingRequest->getProducts())
            );
    }

    private function buildBind($bin): array
    {
        return [
            'id' => $bin->getId(),
            'w' => $bin->getWidth(),
            'h' => $bin->getHeight(),
            'd' => $bin->getLength(),
            'max_wg' => $bin->getMaxWeight(),
        ];
    }

    private function buildItem(PackagingRequestProduct $product): array
    {
        return [
            'id' => $product->getId(),
            'w' => $product->getWidth(),
            'h' => $product->getHeight(),
            'd' => $product->getLength(),
            'wg' => $product->getWeight(),
            'vr' => 1,
            'q' => 1,
        ];
    }
}
