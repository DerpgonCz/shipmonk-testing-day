<?php

declare(strict_types=1);

namespace App\Serialization;

use App\Http\Response\MultiBinPackingResponse;
use App\Http\Response\MultiBinPackingResponseBin;
use App\Http\Response\MultiBinPackingResponseError;
use App\Http\Response\MultiBinPackingResponseItem;

class MultiBinPackingResponseDeserializer
{
    /**
     * @throws \JsonException
     * @throws \Symfony\Component\Validator\Exception\ValidationFailedException
     */
    public function deserialize(string $data): MultiBinPackingResponse
    {
        $jsonData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return $this->buildObjectFromData($jsonData);
    }

    private function buildObjectFromData(array $jsonData): MultiBinPackingResponse
    {
        return (new MultiBinPackingResponse())
            ->setBinsPacked($this->extractBinsPacked($jsonData))
            ->setErrors($this->extractErrors($jsonData))
            ->setNotPackedItems($this->extractNotPackedItems($jsonData));
    }

    private function extractBinsPacked(array $jsonData): array
    {
        $binsPacked = [];
        foreach ($jsonData['response']['bins_packed'] as $packedBin) {
            $packedBinData = $packedBin['bin_data'];
            $binsPacked[] = (new MultiBinPackingResponseBin())
                ->setWidth($packedBinData['w'])
                ->setHeight($packedBinData['h'])
                ->setLength($packedBinData['d'])
                ->setWeight($packedBinData['weight']);
        }

        return $binsPacked;
    }

    private function extractErrors(array $jsonData): array
    {
        $errors = [];
        foreach ($jsonData['response']['errors'] as $error) {
            $errors[] = (new MultiBinPackingResponseError())
                ->setMessage($error['message'])
                ->setLevel($error['level']);
        }

        return $errors;
    }

    private function extractNotPackedItems(array $jsonData): array
    {
        $notPackedItems = [];
        foreach ($jsonData['response']['not_packed_items'] as $notPackedItem) {
            $notPackedItems[] = (new MultiBinPackingResponseItem())
                ->setId($notPackedItem['id'])
                ->setWidth($notPackedItem['w'])
                ->setHeight($notPackedItem['h'])
                ->setLength($notPackedItem['d'])
                ->setWeight($notPackedItem['wg']);
        }

        return $notPackedItems;
    }
}
