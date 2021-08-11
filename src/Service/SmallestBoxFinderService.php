<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Response\MultiBinPackingResponse;
use App\Http\Response\MultiBinPackingResponseBin;

class SmallestBoxFinderService
{
    public function findSmallestBox(MultiBinPackingResponse $packingResponse): ?MultiBinPackingResponseBin
    {
        $smallestBin = null;
        $smallestVolume = PHP_FLOAT_MAX;
        foreach ($packingResponse->getBinsPacked() as $packedBin) {
            $volume = $packedBin->getVolume();
            // If volumes match, we do not have any other criteria anyway
            if ($volume < $smallestVolume) {
                $smallestBin = $packedBin;
                $smallestVolume = $volume;
            }
        }

        return $smallestBin;
    }
}
