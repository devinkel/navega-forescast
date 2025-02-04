<?php

namespace Tests\Services\External;

use Mockery;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use App\Services\External\AbstractApiService;
use App\Exceptions\ExternalApiException;

/**
 * @testdox --AbstractApiService
 *
 */

class AbstractApiServiceTest extends TestCase
{
    protected $mockApiService;
    protected $config;

    /**
     * Inicializa o estado do teste.
     *
     * Cria um mock da classe abstrata AbstractApiService, passando o
     * array de configura o para o construtor. Além disso, substitui o
     * cliente Guzzle por um mock que retorna um Response com um
     * status code 200 e um corpo JSON com o conte do "success". O
     * cliente mockado   substitu do no mock da classe abstrata
     * utilizando reflex o.
     */
    protected function setUp(): void
    {
        $this->config = [
            'url' => 'https://api.exemplo.com',
            'headers' => ['Authorization' => 'Bearer token123'],
            'timeout' => 10
        ];

        // Criar um mock concreto da classe abstrata
        $this->mockApiService = $this->getMockForAbstractClass(
            AbstractApiService::class, 
            [$this->config]
        );

        // Substituir o cliente Guzzle por um mock
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode(['data' => 'success']))
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $mockClient = new Client(['handler' => $handlerStack]);

        // Usar reflexão para substituir o cliente privado
        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);
    }

    /**
     * @testdox Sucesso ao usar a classe abstrata para chamar a API
     *
     */
    public function testSuccessfulApiCall()
    {
        // Usar um método público que chame o método protegido get()
        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->mockApiService, [[]]);

        $this->assertIsArray($result);
        $this->assertEquals(['data' => 'success'], $result);
    }

    /**
     * @testdox Retornar uma exceção com o erro da API
     *
     */
    public function testInvalidJsonResponse()
    {
        // Criar um mock com resposta JSON inválida
        $mockHandler = new MockHandler([
            new Response(200, [], 'Invalid JSON')
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $mockClient = new Client(['handler' => $handlerStack]);

        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);

        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $this->expectException(ExternalApiException::class);
        $this->expectExceptionMessage('Invalid JSON response from API');

        $reflectionMethod->invokeArgs($this->mockApiService, [[]]);
    }

    /**
     * @testdox Retornar uma exceção com o erro da API
     *
     */
    public function testApiRequestException()
    {
        // Simular uma exceção de requisição
        $mockHandler = new MockHandler([
            new \GuzzleHttp\Exception\RequestException(
                'Timeout error', 
                new \GuzzleHttp\Psr7\Request('GET', 'test'),
                new Response(408, [], 'Request Timeout')
            )
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $mockClient = new Client(['handler' => $handlerStack]);

        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);

        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $this->expectException(ExternalApiException::class);

        $reflectionMethod->invokeArgs($this->mockApiService, [[]]);
    }

    /**
     * @testdox Sucesso ao configurar a classe abstrata
     *
     */

    public function testClientConfiguration()
    {
        $config = [
            'url' => 'https://api.exemplo.com',
            'headers' => ['Authorization' => 'Bearer token123'],
            'timeout' => 15
        ];

        $service = $this->getMockForAbstractClass(
            AbstractApiService::class, 
            [$config]
        );

        $reflectionClass = new \ReflectionClass($service);
        $configProperty = $reflectionClass->getProperty('config');
        $configProperty->setAccessible(true);
        $savedConfig = $configProperty->getValue($service);

        $this->assertEquals($config['url'], $savedConfig['url']);
        $this->assertEquals($config['headers'], $savedConfig['headers']);
        $this->assertEquals($config['timeout'], $savedConfig['timeout']);
    }


    /**
     * @testdox Retorna erro de API com timeout
     *
     */

    public function testApiTimeout()
    {
        $mockHandler = new MockHandler([
            new \GuzzleHttp\Exception\ConnectException(
                'Connection timeout',
                new \GuzzleHttp\Psr7\Request('GET', 'test')
            )
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $mockClient = new Client(['handler' => $handlerStack]);

        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);

        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $this->expectExceptionMessage('Connection timeout');

        $reflectionMethod->invokeArgs($this->mockApiService, [[]]);
    }

    /**
     * @testdox Retorna erro 500
     *
     */
    public function testApiErrorResponse()
    {
        $mockHandler = new MockHandler([
            new Response(500, [], json_encode(['error' => 'Internal Server Error']))
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $mockClient = new Client(['handler' => $handlerStack]);

        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);

        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $this->expectException(ExternalApiException::class);
        $this->expectExceptionMessage('API Error: 500');

        $reflectionMethod->invokeArgs($this->mockApiService, [[]]);
    }

    /**
     * @testdox Sucesso ao passar parametros
     *
     */
    public function testRequestParameters()
    {
        $params = ['param1' => 'value1', 'param2' => 'value2'];

        $mockHandler = new MockHandler([
            new Response(200, [], json_encode(['data' => 'success']))
        ]);
        
        $mockClient = Mockery::mock(Client::class);

        $mockClient
            ->shouldReceive('get')
            ->with($this->config['url'], [
                'query' => $params,
                'timeout' => $this->config['timeout'],
                'connect_timeout' => 10,
                'headers' => $this->config['headers']
            ])
            ->once()
            ->andReturn(new Response(200, [], json_encode(['data' => 'success'])));

        $reflectionClass = new \ReflectionClass($this->mockApiService);
        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->mockApiService, $mockClient);

        $reflectionMethod = new \ReflectionMethod($this->mockApiService, 'get');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->mockApiService, [$params]);

        $this->assertEquals(['data' => 'success'], $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}