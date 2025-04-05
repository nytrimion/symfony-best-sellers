<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Book\Repository;

use App\Domain\Book\Dto\BestSellersQuery;
use App\Domain\Book\Repository\BookRepositoryException;
use App\Infrastructure\Book\Repository\NewYorkTimesBookRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;

final class NewYorkTimesBookRepositoryTest extends TestCase
{
    private NewYorkTimesBookRepository $sut;
    private MockHttpClient $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new MockHttpClient();

        $this->sut = new NewYorkTimesBookRepository(
            $this->httpClient,
            'https://api.example.com',
            '12345',
        );
    }

    public function testGetBestSellersReturnsResponseWhenNewYorkTimesApiResponseIsSuccessful(): void
    {
        $apiResponse = new JsonMockResponse(['foo' => 'bar']);

        $this->httpClient->setResponseFactory($apiResponse);

        $response = $this->sut->getBestSellers(new BestSellersQuery());

        $this->assertSame('GET', $apiResponse->getRequestMethod());
        $this->assertSame('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345', $apiResponse->getRequestUrl());
        $this->assertSame(['Accept: application/json'], $apiResponse->getRequestOptions()['headers']);
        $this->assertSame(['foo' => 'bar'], $response->json);
    }

    public function testGetBestSellersReturnsResponseWithAllQueryParameters(): void
    {
        $apiResponse = new JsonMockResponse();

        $this->httpClient->setResponseFactory($apiResponse);

        $this->sut->getBestSellers(new BestSellersQuery(
            author: 'John',
            title: 'Whatever',
            isbn: ['0553293389', '9780553293388'],
            offset: 40,
        ));

        $this->assertStringEndsWith('?author=John&title=Whatever&isbn=0553293389;9780553293388&offset=40&api-key=12345', $apiResponse->getRequestUrl());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenHttpClientConnectionHasFailed(): void
    {
        $apiResponse = new MockResponse(info: ['error' => 'host unreachable']);

        $this->httpClient->setResponseFactory($apiResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new BestSellersQuery());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseHasFailed(): void
    {
        $apiResponse = new MockResponse(info: ['http_code' => 429]);

        $this->httpClient->setResponseFactory($apiResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(429);

        $this->sut->getBestSellers(new BestSellersQuery());
    }
    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseHasNoJsonContent(): void
    {
        $apiResponse = new MockResponse([new \RuntimeException('Failed to read json content')]);

        $this->httpClient->setResponseFactory($apiResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new BestSellersQuery());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseIsScalar(): void
    {
        $apiResponse = new JsonMockResponse('"string"');

        $this->httpClient->setResponseFactory($apiResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new BestSellersQuery());
    }
}
