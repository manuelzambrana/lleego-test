<?php

namespace App\Application\UseCase;

use App\Infrastructure\HttpClient\AvailabilityClient;

class FetchAvailabilityUseCase
{
    private AvailabilityClient $client;

    public function __construct(AvailabilityClient $client)
    {
        $this->client = $client;
    }

    public function execute(string $origin, string $destination, string $date): array
    {

        return $this->client->fetch($origin, $destination, $date);
    }
}
