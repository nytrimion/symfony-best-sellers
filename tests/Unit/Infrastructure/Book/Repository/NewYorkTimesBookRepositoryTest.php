<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Book\Repository;

use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;
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
        $httpResponse = new JsonMockResponse(['foo' => 'bar']);

        $this->httpClient->setResponseFactory($httpResponse);

        $actual = $this->sut->getBestSellers(new GetBestSellersQuery());

        $this->assertSame('GET', $httpResponse->getRequestMethod());
        $this->assertSame('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345', $httpResponse->getRequestUrl());
        $this->assertSame(['Accept: application/json'], $httpResponse->getRequestOptions()['headers']);
        $this->assertSame(['foo' => 'bar'], $actual);
    }

    public function testGetBestSellersReturnsResponseWithAllQueryParameters(): void
    {
        $httpResponse = new JsonMockResponse();

        $this->httpClient->setResponseFactory($httpResponse);

        $this->sut->getBestSellers(new GetBestSellersQuery(
            author: 'John',
            title: 'Whatever',
            isbn: ['0553293389', '9780553293388'],
            offset: 40,
        ));

        $this->assertStringEndsWith('?author=John&title=Whatever&isbn=0553293389;9780553293388&offset=40&api-key=12345', $httpResponse->getRequestUrl());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenHttpClientConnectionHasFailed(): void
    {
        $httpResponse = new MockResponse(info: ['error' => 'host unreachable']);

        $this->httpClient->setResponseFactory($httpResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new GetBestSellersQuery());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseHasFailed(): void
    {
        $httpResponse = new MockResponse(info: ['http_code' => 429]);

        $this->httpClient->setResponseFactory($httpResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(429);

        $this->sut->getBestSellers(new GetBestSellersQuery());
    }
    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseHasNoJsonContent(): void
    {
        $httpResponse = new MockResponse([new \RuntimeException('Failed to read json content')]);

        $this->httpClient->setResponseFactory($httpResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new GetBestSellersQuery());
    }

    public function testGetBestSellersThrowsBookRepositoryExceptionWhenNewYorkTimesApiResponseIsScalar(): void
    {
        $httpResponse = new JsonMockResponse('"string"');

        $this->httpClient->setResponseFactory($httpResponse);

        $this->expectException(BookRepositoryException::class);
        $this->expectExceptionMessage('https://api.example.com/books/v3/lists/best-sellers/history.json?api-key=12345');
        $this->expectExceptionCode(0);

        $this->sut->getBestSellers(new GetBestSellersQuery());
    }
}
