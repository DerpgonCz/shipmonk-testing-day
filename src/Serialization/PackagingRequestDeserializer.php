<?php

declare(strict_types=1);

namespace App\Serialization;

use App\DTO\PackagingRequest;
use App\DTO\PackagingRequestProduct;
use App\Validation\PackagingRequestValidator;

class PackagingRequestDeserializer
{
    private PackagingRequestValidator $validator;

    public function __construct(
        PackagingRequestValidator $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @throws \JsonException
     * @throws \Symfony\Component\Validator\Exception\ValidationFailedException
     */
    public function deserialize(string $data): PackagingRequest
    {
        $jsonData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $this->validator->validate($jsonData);

        return $this->buildObjectFromData($jsonData);
    }

    private function buildObjectFromData(array $jsonData): PackagingRequest
    {
        $products = [];
        foreach ($jsonData['products'] as $product) {
            $products[] = (new PackagingRequestProduct())
                ->setId($product['id'])
                ->setWidth($product['width'])
                ->setHeight($product['height'])
                ->setLength($product['length'])
                ->setWeight($product['weight']);
        }

        return (new PackagingRequest())
            ->setProducts($products);
    }
}
