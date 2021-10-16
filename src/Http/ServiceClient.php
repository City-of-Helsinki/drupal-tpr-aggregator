<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Utils;

final class ServiceClient
{
    public const BASE_URI = 'https://www.hel.fi/palvelukarttaws/rest/vpalvelurekisteri/description/';

    public function __construct(private ClientInterface $httpClient)
    {
    }

    public function get(int $id, string $language): ?\stdClass
    {
        try {
            $response = $this->httpClient->request('GET', (string) $id, [
                'query' => ['language' => $language],
            ]);

            return Utils::jsonDecode($response->getBody()->getContents());
        } catch (RequestException $e) {
        }
        return null;
    }

    public function all(string $language = null): array
    {
        $query = $language ? ['language' => $language] : [];

        $response = $this->httpClient->request('GET', '', [
            'query' => $query,
        ]);

        return Utils::jsonDecode($response->getBody()->getContents());
    }
}
