<?php

declare(strict_types=1);

namespace App\Http\Client;

use App\Http\Request\MultiBinPackingRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\SerializerInterface;

class PackagingClient
{
    private const URL_MULTI_BIN_PACKING = '/packer/packIntoMany';

    private Client $packagingClient;

    private SerializerInterface $serializer;

    public function __construct(
        Client $packagingClient,
        SerializerInterface $serializer
    ) {
        $this->packagingClient = $packagingClient;
        $this->serializer = $serializer;
    }

    public function requestMultiBinPacking(MultiBinPackingRequest $request): Response
    {
        $requestBody = $this->serializer->serialize($request, 'json');

        return $this->packagingClient->post(
            self::URL_MULTI_BIN_PACKING,
            [RequestOptions::BODY => $requestBody]
        );
    }
}
