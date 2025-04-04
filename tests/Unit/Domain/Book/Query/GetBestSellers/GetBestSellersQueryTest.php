<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Book\Query\GetBestSellers;

use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;
use PHPUnit\Framework\TestCase;

final class GetBestSellersQueryTest extends TestCase
{
    public function testItContainsGivenValues(): void
    {
        $sut = new GetBestSellersQuery(
            author: 'Isaac Asimov',
            title: 'Foundation',
            isbn: ['foo', 'bar'],
            offset: 20,
        );

        $this->assertSame('Isaac Asimov', $sut->author);
        $this->assertSame('Foundation', $sut->title);
        $this->assertSame(['foo', 'bar'], $sut->isbn);
        $this->assertSame(20, $sut->offset);
    }

    public function testItContainsDefaultValues(): void
    {
        $sut = new GetBestSellersQuery();

        $this->assertSame('', $sut->author);
        $this->assertSame('', $sut->title);
        $this->assertSame([], $sut->isbn);
        $this->assertSame(0, $sut->offset);
    }

    public function testItTrimsAuthor(): void
    {
        $sut = new GetBestSellersQuery(author: '  whatever  ');

        $this->assertSame('whatever', $sut->author);
    }

    public function testItTrimsTitle(): void
    {
        $sut = new GetBestSellersQuery(title: '  whatever  ');

        $this->assertSame('whatever', $sut->title);
    }

    public function testItRemovesIsbnDuplicates(): void
    {
        $sut = new GetBestSellersQuery(isbn: [
            'foo',
            'bar',
            'foo',
            'baz',
            'bar',
        ]);
        $this->assertSame([
            'foo',
            'bar',
            'baz',
        ], $sut->isbn);
    }
}