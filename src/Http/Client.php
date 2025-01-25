<?php

declare(strict_types=1);

namespace Shipy\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Shipy\Exception\ApiException;

class Client
{
    private const API_BASE_URL = 'https://api.shipy.dev/pay';
    private const API_TIMEOUT = 30;
    private readonly GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient([
            'base_uri' => self::API_BASE_URL,
            'timeout' => self::API_TIMEOUT,
            'http_errors' => false
        ]);
    }

    public function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'form_params' => $data
            ]);

            $body = (string) $response->getBody();
            $result = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $result;
        } catch (GuzzleException $e) {
            throw new ApiException('HTTP request failed: ' . $e->getMessage());
        }
    }
} 