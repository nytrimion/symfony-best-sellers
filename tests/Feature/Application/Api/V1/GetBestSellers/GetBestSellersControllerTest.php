<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Api\V1\GetBestSellers;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetBestSellersControllerTest extends WebTestCase
{
    private const string ENDPOINT_URL = '/api/v1/best-sellers';

    private KernelBrowser $client;
    private HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->httpClient = new MockHttpClient();

        $this->getContainer()->set(HttpClientInterface::class, $this->httpClient);
    }

    public function testItReturnsSuccessfulResponseWithNoGivenParameters(): void
    {
        $apiResponse = new JsonMockResponse(['status' => 'OK']);

        $this->httpClient->setResponseFactory($apiResponse);

        $this->client->jsonRequest('GET', self::ENDPOINT_URL);

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertSame('OK', $json['status']);
    }

    public function testItReturnsSuccessfulResponseWithAllGivenParameters(): void
    {
        $apiResponse = new JsonMockResponse(['status' => 'OK']);

        $this->httpClient->setResponseFactory($apiResponse);

        $this->client->jsonRequest('GET', self::ENDPOINT_URL . '?author=John&title=Whatever&isbn[]=0553293389&isbn[]=9780553293388&offset=20');

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertSame('OK', $json['status']);
    }

    public function testItReturnsValidationErrorsWhenGivenParameterAreInvalid(): void
    {
        $this->client->jsonRequest('GET', self::ENDPOINT_URL . '?isbn[]=055329338&isbn[]=97805532933888&offset=10');

        $response = $this->client->getResponse();
        $this->assertSame(422, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertSame(422, $json['status']);
        $this->assertSame('Validation Failed', $json['title']);
        $this->assertStringContainsString('isbn[0]: This value is neither a valid ISBN-10 nor a valid ISBN-13', $json['detail']);
        $this->assertStringContainsString('isbn[1]: This value is neither a valid ISBN-10 nor a valid ISBN-13', $json['detail']);
        $this->assertStringContainsString('offset: This value should be a multiple of 20', $json['detail']);
    }
}
