<?php

namespace App;

use App\Exception\PackingException;
use App\Serialization\PackagingRequestDeserializer;
use App\Service\PackagingService;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class Application
{
    private PackagingService $packagingService;
    private PackagingRequestDeserializer $packagingRequestDeserializer;
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        PackagingService $packagingService,
        PackagingRequestDeserializer $packagingRequestDeserializer
    ) {
        $this->serializer = $serializer;
        $this->packagingService = $packagingService;
        $this->packagingRequestDeserializer = $packagingRequestDeserializer;
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getBody()->getContents();

        try {
            $requestDto = $this->packagingRequestDeserializer->deserialize($requestBody);
        } catch (JsonException $exception) {
            return new Response(422, [], json_encode(['message' => 'Malformed input: Invalid JSON']));
        } catch (ValidationFailedException $exception) {
            return new Response(422, [], json_encode(['message' => sprintf('Invalid input: %s', $exception->getMessage())]));
        }

        try {
            $packing = $this->packagingService->packageInMultipleBins($requestDto);
        } catch (JsonException | ValidationFailedException $exception) {
            return new Response(500, [], json_encode(['message' => sprintf('Internal server error: %s', $exception->getMessage())]));
        } catch (PackingException $exception) {
            return new Response(500, [], json_encode(['message' => $exception->getMessage()]));
        }

        // TODO: Do not include ID and Cache Key in response?
        return new Response(200, [], $this->serializer->serialize($packing, 'json'));
    }

}
