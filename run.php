<?php

use App\Application;
use App\Factory\BasePackagingClientFactory;
use App\Factory\PackagingRequestFactory;
use App\Http\Client\PackagingClient;
use App\Serialization\MultiBinPackingResponseDeserializer;
use App\Serialization\PackagingRequestDeserializer;
use App\Service\PackagingService;
use App\Service\PackingCachingService;
use App\Service\SmallestBoxFinderService;
use App\Validation\PackagingRequestValidator;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\Dotenv\Dotenv;

/** @var EntityManager $entityManager */
$entityManager = require __DIR__.'/src/bootstrap.php';

// Load env
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Bootstrap
$serializer = JMS\Serializer\SerializerBuilder::create()->build();

$application = new Application(
    $serializer,
    new PackagingService(
        new PackagingClient(
            (new BasePackagingClientFactory())->createClient(
                $_ENV['PACKAGING_API_BASE_URL'],
                $_ENV['PACKAGING_API_USERNAME'],
                $_ENV['PACKAGING_API_KEY']
            ),
            $serializer
        ),
        $entityManager,
        new PackagingRequestFactory(),
        new MultiBinPackingResponseDeserializer(),
        new PackingCachingService(
            $entityManager,
            $serializer
        ),
        new SmallestBoxFinderService()
    ),
    new PackagingRequestDeserializer(
        new PackagingRequestValidator()
    )
);

// Run request
$readFromStdIn = static function (): string {
    $f = fopen('php://stdin', 'r');

    $content = '';
    while ($line = fgets($f)) {
        $content .= $line;
    }
    fclose($f);

    return $content;
};

$body = $argv[1] ?? $readFromStdIn();

$request = new Request('POST', new Uri('http://localhost/pack'), ['Content-Type' => 'application/json'], $body);
$response = $application->run($request);

echo "<<< In:\n".Message::toString($request)."\n\n";
echo ">>> Out:\n".Message::toString($response)."\n\n";
