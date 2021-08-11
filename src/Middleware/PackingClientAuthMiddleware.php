<?php

declare(strict_types=1);

namespace App\Middleware;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;

class PackingClientAuthMiddleware
{
    private string $apiUsername;
    private string $apiKey;

    public function __construct(string $apiUsername, string $apiKey)
    {
        $this->apiUsername = $apiUsername;
        $this->apiKey = $apiKey;
    }

    public function __invoke(callable $handler): callable
    {
        return function (
            RequestInterface $request,
            array $options
        ) use ($handler) {
            $requestBody = json_decode($request->getBody()->getContents(), true, JSON_THROW_ON_ERROR);
            $newRequestBody = array_merge($requestBody, [
                'username' => $this->apiUsername,
                'api_key' => $this->apiKey,
            ]);
            $newRequest = $request->withBody(Utils::streamFor(json_encode($newRequestBody)));

            return $handler($newRequest, $options);
        };
    }
}
