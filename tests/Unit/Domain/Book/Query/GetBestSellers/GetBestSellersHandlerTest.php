<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Book\Query\GetBestSellers;

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
        $this
            ->bookRepository
            ->method('getBestSellers')
            ->willReturn(['status' => 'OK']);

        $response = $this->sut->handle(new GetBestSellersQuery());

        $this->assertSame(['status' => 'OK'], $response->payload);
    }

    public function testItThrowsBookRepositoryExceptionWhenBookRepositoryThrowsBookRepositoryException(): void
    {
        $exception = new BookRepositoryException();
        $this
            ->bookRepository
            ->method('getBestSellers')
            ->willThrowException($exception);

        $this->expectExceptionObject($exception);

        $this->sut->handle(new GetBestSellersQuery());
    }
}