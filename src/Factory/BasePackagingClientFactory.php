<?php

declare(strict_types=1);

namespace App\Factory;

use App\Middleware\PackingClientAuthMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class BasePackagingClientFactory
{
    public function createClient(
        string $baseUri,
        string $apiUsername = '',
        string $apiKey = ''
    ): Client {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(
            new PackingClientAuthMiddleware(
                $apiUsername,
                $apiKey
            )
        );

        return new Client([
            'base_uri' => $baseUri,
            'handler' => $stack,
        ]);
    }
}
