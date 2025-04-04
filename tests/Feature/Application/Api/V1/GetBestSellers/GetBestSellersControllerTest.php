<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Api\V1\GetBestSellers;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetBestSellersControllerTest extends WebTestCase
{
    private const string ENDPOINT_URL = '/api/v1/best-sellers';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    public function testItReturnsSuccessfulResponseWithNoGivenParameters(): void
    {
        $this->client->jsonRequest('GET', self::ENDPOINT_URL);
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('OK', $responseData['status']);
    }

    public function testItReturnsSuccessfulResponseWithAllGivenParameters(): void
    {
        $this->client->jsonRequest('GET', self::ENDPOINT_URL . '?author=John&title=Whatever&isbn[]=0553293389&isbn[]=9780553293388&offset=20');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('OK', $responseData['status']);
    }

    public function testItReturnsValidationErrorsWhenGivenParameterAreInvalid(): void
    {
        $this->client->jsonRequest('GET', self::ENDPOINT_URL . '?isbn[]=055329338&isbn[]=97805532933888&offset=10');
        $response = $this->client->getResponse();
        $this->assertSame(422, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(422, $responseData['status']);
        $this->assertSame('Validation Failed', $responseData['title']);
        $this->assertStringContainsString('isbn[0]: This value is neither a valid ISBN-10 nor a valid ISBN-13', $responseData['detail']);
        $this->assertStringContainsString('isbn[1]: This value is neither a valid ISBN-10 nor a valid ISBN-13', $responseData['detail']);
        $this->assertStringContainsString('offset: This value should be a multiple of 20', $responseData['detail']);
    }
}
