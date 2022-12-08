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

    public function get(string $endpoint, int $id, string $language, int $attempt = 0): array
    {
        $this->assertEndpoint($endpoint);

        if ($attempt < 2) {
            try {
                $response = $this->httpClient->request('GET', "$endpoint/$id", [
                    'query' => ['language' => $language],
                    'headers' => ['accept' => 'application/json'],
                    'curl' => [CURLOPT_TCP_KEEPALIVE => true],
                ]);

                return Utils::jsonDecode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                if ($e->getResponse()?->getStatusCode() === 404) {
                    return [];
                }
                // Attempt to re-fetch data again.
                return $this->get($endpoint, $id, $language, ++$attempt);
            }
        }
        return [];
    }

    public function all(string $endpoint, string $language = null): array
    {
        $this->assertEndpoint($endpoint);
        $query = $language ? ['language' => $language] : [];

        $response = $this->httpClient->request('GET', "$endpoint/", [
            'query' => $query,
            'headers' => ['accept' => 'application/json'],
        ]);

        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }
}
