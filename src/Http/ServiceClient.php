<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Utils;

final class ServiceClient
{
    public const BASE_URI = 'https://www.hel.fi/palvelukarttaws/rest/vpalvelurekisteri/';

    public function __construct(private ClientInterface $httpClient)
    {
    }

    private function assertEndpoint(string $endpoint): void
    {
        if (!in_array($endpoint, ['description', 'errandservice'])) {
            throw new \InvalidArgumentException('Invalid endpoint: ' . $endpoint);
        }
    }

    public function get(string $endpoint, int $id, string $language): array
    {
        $this->assertEndpoint($endpoint);

        try {
            $response = $this->httpClient->request('GET', "$endpoint/$id", [
                'query' => ['language' => $language],
            ]);

            return Utils::jsonDecode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
        }
        return [];
    }

    public function all(string $endpoint, string $language = null): array
    {
        $this->assertEndpoint($endpoint);
        $query = $language ? ['language' => $language] : [];

        $response = $this->httpClient->request('GET', "$endpoint/", [
            'query' => $query,
        ]);

        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }
}
