<?php

namespace Nur\Pathao\Helpers;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Nur\Pathao\Exceptions\PathaoException;

class TokenManager
{
    public static function getToken(array $config): string
    {
        return Cache::remember('pathao_token', 7200, function () use ($config) {
            $client = new Client(['base_uri' => self::baseUrl($config)]);

            try {
                $response = $client->post('aladdin/api/v1/issue-token', [
                    'json' => [
                        'client_id' => $config['client_id'],
                        'client_secret' => $config['client_secret'],
                        'username' => $config['username'],
                        'password' => $config['password']
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                return $data['access_token'] ?? throw new PathaoException('Invalid token response');
            } catch (\Exception $e) {
                throw new PathaoException('Failed to fetch Pathao token: ' . $e->getMessage());
            }
        });
    }

    private static function baseUrl(array $config): string
    {
        return $config['sandbox']
            ? $config['base_urls']['sandbox']
            : $config['base_urls']['live'];
    }
}
