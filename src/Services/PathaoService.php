<?php

namespace Nur\Pathao\Services;

use GuzzleHttp\Client;
use Nur\Pathao\Helpers\TokenManager;
use Nur\Pathao\Exceptions\PathaoException;

class PathaoService
{
    protected Client $client;
    protected array $config;

    public function __construct()
    {
        $this->config = config('pathao');
        $this->client = new Client(['base_uri' => $this->baseUrl()]);
    }

    protected function baseUrl(): string
    {
        return $this->config['sandbox']
            ? $this->config['base_urls']['sandbox']
            : $this->config['base_urls']['live'];
    }

    protected function headers(): array
    {
        $token = TokenManager::getToken($this->config);

        return [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json; charset=UTF-8',
        ];
    }

    protected function get(string $endpoint)
    {
        try {
            $response = $this->client->get($endpoint, ['headers' => $this->headers()]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new PathaoException($e->getMessage());
        }
    }

    // === Public APIs ===
    public function cities()
    {
        return $this->get('aladdin/api/v1/city-list')['data']['data'] ?? [];
    }

    public function zones(int $cityId)
    {
        return $this->get("aladdin/api/v1/cities/{$cityId}/zone-list")['data']['data'] ?? [];
    }

    public function areas(int $zoneId)
    {
        return $this->get("aladdin/api/v1/zones/{$zoneId}/area-list")['data']['data'] ?? [];
    }
}
