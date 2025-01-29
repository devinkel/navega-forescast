<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use App\Exceptions\ExternalApiException;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractApiService
{
    protected Client $client;
    protected $config;

    public function __construct()
    {
        $this->config = config('ocean_client_api');
        $this->client = new Client([
            'base_uri' => $this->config['url'],
            'headers' => $this->config['headers'],
            'timeout' => $this->config['timeout'],
            'http_erros' => true,
            'verify' => true,
            'connect_timeout' => 10
        ]);
    }
    protected function get(array $params = []): array
    {
        try {
            $response = $this->client->get(config('ocean_client_api.url'), [
                'query' => $params,
                'timeout' => $this->config['timeout'],
                'connect_timeout' => 10,
                'headers' => $this->config['headers']
            ]);

            return $this->handleResponse($response);
        } catch (RequestException $e) {
            dd($e);
            // Guzzle 7 possui tratamento melhorado para erros de request
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $body = $e->getResponse()->getBody()->getContents();
                throw new ExternalApiException(
                    "API Error: {$statusCode} - {$body}",
                    $statusCode
                );
            }
            throw new ExternalApiException($e->getMessage(), 500);
        }
    }

    protected function handleResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExternalApiException('Invalid JSON response from API');
        }

        return $data;
    }
}
