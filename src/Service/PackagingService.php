<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PackagingRequest;
use App\Entity\Packaging;
use App\Entity\Packing;
use App\Exception\PackingException;
use App\Factory\PackagingRequestFactory;
use App\Http\Client\PackagingClient;
use App\Http\Response\MultiBinPackingResponseError;
use App\Http\Response\MultiBinPackingResponseItem;
use App\Serialization\MultiBinPackingResponseDeserializer;
use Doctrine\ORM\EntityManager;
use LogicException;

class PackagingService
{
    private PackagingClient $packagingClient;
    private EntityManager $entityManager;
    private PackagingRequestFactory $packagingRequestFactory;
    private MultiBinPackingResponseDeserializer $multiBinPackingResponseDeserializer;
    private PackingCachingService $packingCachingService;
    private SmallestBoxFinderService $smallestBoxFinderService;

    public function __construct(
        PackagingClient $packagingClient,
        EntityManager $entityManager,
        PackagingRequestFactory $packagingRequestFactory,
        MultiBinPackingResponseDeserializer $multiBinPackingResponseDeserializer,
        PackingCachingService $packingCachingService,
        SmallestBoxFinderService $smallestBoxFinderService
    ) {
        $this->packagingClient = $packagingClient;
        $this->entityManager = $entityManager;
        $this->packagingRequestFactory = $packagingRequestFactory;
        $this->multiBinPackingResponseDeserializer = $multiBinPackingResponseDeserializer;
        $this->packingCachingService = $packingCachingService;
        $this->smallestBoxFinderService = $smallestBoxFinderService;
    }

    /**
     * @throws \App\Exception\PackingException
     * @throws \JsonException
     */
    public function packageInMultipleBins(
        PackagingRequest $input
    ): Packing {
        $cachedData = $this->packingCachingService->retrieve($input);
        if ($cachedData !== null) {
            return $cachedData;
        }

        $bins = $this->entityManager->getRepository(Packaging::class)->findAll();
        $multiBinPackingRequest = $this->packagingRequestFactory->createMultiBinPackingRequest(
            $input,
            $bins
        );

        $response = $this->packagingClient->requestMultiBinPacking($multiBinPackingRequest);
        $responseDto = $this->multiBinPackingResponseDeserializer->deserialize($response->getBody()->getContents());

        if (!$responseDto->hasBinsPacked()) {
            throw new PackingException('No bins packed');
        }

        if ($responseDto->hasNotPackedItems()) {
            $notPackedIds = array_map(fn(MultiBinPackingResponseItem $item): int => $item->getId(), $responseDto->getNotPackedItems());
            throw new PackingException(sprintf('Items with ID %s were not packed', implode(',', $notPackedIds)));
        }

        // TODO: Investigate which errors in API can be treated as actual errors
        // TODO: What to do when item cannot be packed?
        if ($responseDto->hasErrors()) {
            $messages = array_map(
                fn(MultiBinPackingResponseError $error) => sprintf('%s (%s)', $error->getMessage(), $error->getLevel()),
                $responseDto->getErrors()
            );

            throw new PackingException(sprintf('Internal server error: %s', implode(', ', $messages)));
        }

        $smallestBox = $this->smallestBoxFinderService->findSmallestBox($responseDto);
        if ($smallestBox === null) {
            // This should never happen
            throw new LogicException('No smallest box found');
        }

        $packing = $this->packingCachingService->save($input, $smallestBox);

        return $packing;
    }
}
