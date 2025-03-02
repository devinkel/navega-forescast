<?php

namespace App\Services\External;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Exceptions\ExternalApiException;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractApiService
{
    protected Client $client;
    protected $config;

/**
 * Initialize the API service with the given configuration.
 *
 * @param array $config Configuration array containing 'url', 'headers', and 'timeout'.
 */

    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->config['url'],
            'headers' => $this->config['headers'],
            'timeout' => $this->config['timeout'],
            'http_erros' => true,
            'verify' => true,
            'connect_timeout' => 10
        ]);
    }
    /**
     * Send a GET request to the API and return the parsed response.
     * 
     * @param array $params Query parameters to be sent with the request.
     * 
     * @return array Parsed response from the API.
     * 
     * @throws ExternalApiException If the API request fails.
     */
    protected function get(array $params = []): array
    {
        try {
            $response = $this->client->get($this->config['url'], [
                'query' => $params,
                'timeout' => $this->config['timeout'],
                'connect_timeout' => 10,
                'headers' => $this->config['headers']
            ]);

            return $this->handleResponse($response);
        } catch (RequestException $e) {
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

    /**
     * Parse the response from the API and return the data as an array.
     *
     * @param ResponseInterface $response API response.
     *
     * @return array Parsed response data.
     *
     * @throws ExternalApiException If the JSON response is invalid.
     */
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
