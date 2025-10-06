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

    protected function post(string $endpoint, array $body)
    {
        try {
            $response = $this->client->post($endpoint, [
                'headers' => $this->headers(),
                'json' => $body,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new PathaoException($e->getMessage());
        }
    }

    // === Public APIs ===

    public function cities(): array
    {
        return $this->get('aladdin/api/v1/city-list')['data']['data'] ?? [];
    }

    public function zones(int $cityId): array
    {
        return $this->get("aladdin/api/v1/cities/{$cityId}/zone-list")['data']['data'] ?? [];
    }

    public function areas(int $zoneId): array
    {
        return $this->get("aladdin/api/v1/zones/{$zoneId}/area-list")['data']['data'] ?? [];
    }

    // === New Store API ===
    public function createStore(array $data): array
    {
        $requiredFields = [
            'name', 'contact_name', 'contact_number',
            'address', 'city_id', 'zone_id',
            'area_id'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new PathaoException("Missing required field: {$field}");
            }
        }

        return $this->post('/aladdin/api/v1/stores', $data);
    }

    // === New Order API ===
    public function createOrder(array $data): array
    {
        $requiredFields = [
            'store_id', 'recipient_name', 'recipient_phone',
            'recipient_address', 'delivery_type', 'item_type',
            'item_quantity', 'item_weight', 'amount_to_collect',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new PathaoException("Missing required field: {$field}");
            }
        }

        return $this->post('aladdin/api/v1/orders', $data);
    }

    // === Optional Bulk Order ===
    public function createBulkOrder(array $orders): array
    {
        if (empty($orders)) {
            throw new PathaoException('Orders array cannot be empty');
        }

        return $this->post('aladdin/api/v1/orders/bulk', ['orders' => $orders]);
    }
    
    // === Order Info ===
    public function orderInfo(string $consignmentId): array
    {
        return $this->get("aladdin/api/v1/orders/{$consignmentId}/info") ?? [];
    }
    
    // === Price Calculator ===
    public function priceCalculator(array $data): array
    {
        $requiredFields = [
            'store_id', 'item_type', 'delivery_type',
            'item_weight', 'recipient_city', 'recipient_zone'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new PathaoException("Missing required field: {$field}");
            }
        }

        return $this->post('/aladdin/api/v1/merchant/price-plan', $data);
    }

    // === Store Info ===
    public function storeInfo(): array
    {
        return $this->get("/aladdin/api/v1/stores")['data']['data'] ?? [];
    }
}
