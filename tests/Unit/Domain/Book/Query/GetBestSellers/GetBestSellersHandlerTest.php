<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Book\Query\GetBestSellers;

use App\Domain\Book\Dto\BestSellersQuery;
use App\Domain\Book\Dto\BestSellersResponse;
use App\Domain\Book\Query\GetBestSellers\GetBestSellersHandler;
use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;
use App\Domain\Book\Repository\BookRepository;
use App\Domain\Book\Repository\BookRepositoryException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetBestSellersHandlerTest extends TestCase
{
    private BookRepository&MockObject $bookRepository;
    private GetBestSellersHandler $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);

        $this->sut = new GetBestSellersHandler(
            $this->bookRepository,
        );
    }

    public function testItReturnsResponseWhenBookRepositoryReturnsResults(): void
    {
        $query = new GetBestSellersQuery();
        $this
            ->bookRepository
            ->method('getBestSellers')
            ->willReturn(new BestSellersResponse(['status' => 'OK']));

        $response = $this->sut->handle($query);

        $this->assertSame(['status' => 'OK'], $response->json);
    }

    public function testItInvokesBookRepositoryWithExpectedQuery(): void
    {
        $query = new GetBestSellersQuery(
            author: 'John',
            title: 'Whatever',
            isbn: ['0553293389', '9780553293388'],
            offset: 40,
        );
        $this
            ->bookRepository
            ->expects($this->once())
            ->method('getBestSellers')
            ->with($this->equalTo(new BestSellersQuery(
                author: 'John',
                title: 'Whatever',
                isbn: ['0553293389', '9780553293388'],
                offset: 40,
            )))
            ->willReturn(new BestSellersResponse([]));

        $this->sut->handle($query);
    }

    public function testItTrimsAuthorFromQuery(): void
    {
        $query = new GetBestSellersQuery(author: '  whatever  ');
        $this
            ->bookRepository
            ->expects($this->once())
            ->method('getBestSellers')
            ->with($this->callback(
                static fn(BestSellersQuery $apiQuery): bool => $apiQuery->author === 'whatever',
            ))
            ->willReturn(new BestSellersResponse([]));

        $this->sut->handle($query);
    }

    public function testItTrimsTitleFromQuery(): void
    {
        $query = new GetBestSellersQuery(title: '  whatever  ');
        $this
            ->bookRepository
            ->expects($this->once())
            ->method('getBestSellers')
            ->with($this->callback(
                static fn(BestSellersQuery $apiQuery): bool => $apiQuery->title === 'whatever',
            ))
            ->willReturn(new BestSellersResponse([]));

        $this->sut->handle($query);
    }

    public function testItRemovesIsbnDuplicatesFromQuery(): void
    {
        $query = new GetBestSellersQuery(isbn: [
            'foo',
            'bar',
            'foo',
            'baz',
            'bar',
        ]);
        $this
            ->bookRepository
            ->expects($this->once())
            ->method('getBestSellers')
            ->with($this->callback(
                static fn(BestSellersQuery $apiQuery): bool => $apiQuery->isbn === [
                    'foo',
                    'bar',
                    'baz',
                ],
            ))
            ->willReturn(new BestSellersResponse([]));

        $this->sut->handle($query);
    }

    public function testItThrowsBookRepositoryExceptionWhenBookRepositoryThrowsBookRepositoryException(): void
    {
        $query = new GetBestSellersQuery();
        $exception = new BookRepositoryException();
        $this
            ->bookRepository
            ->method('getBestSellers')
            ->willThrowException($exception);

        $this->expectExceptionObject($exception);

        $this->sut->handle($query);
    }
}