<?php

declare(strict_types=1);

namespace App\Service;


use App\DTO\PackagingRequest;
use App\Entity\Packing;
use App\Http\Response\MultiBinPackingResponseBin;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\ArrayTransformerInterface;

class PackingCachingService
{
    private EntityManager $entityManager;

    private ArrayTransformerInterface $serializer;

    public function __construct(
        EntityManager $entityManager,
        ArrayTransformerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function save(PackagingRequest $request, MultiBinPackingResponseBin $item): Packing
    {
        $key = $this->createKey($request);

        // TODO: Factory
        $packing = new Packing(
            $key,
            $item->getWidth(),
            $item->getHeight(),
            $item->getLength(),
            $item->getWeight()
        );

        $this->entityManager->persist($packing);
        $this->entityManager->flush();

        return $packing;
    }

    private function createKey(PackagingRequest $request): string
    {
        $array = $this->serializer->toArray($request->getProducts());
        usort($array, static fn(array $a, array $b): int => $a['id'] <=> $b['id']);
        foreach ($array as &$item) {
            ksort($item, SORT_STRING);
        }

        return md5(json_encode($array));
    }

    public function retrieve(PackagingRequest $request): ?Packing
    {
        $key = $this->createKey($request);

        return $this->entityManager->getRepository(Packing::class)->findOneBy(['cacheKey' => $key]);
    }
}
