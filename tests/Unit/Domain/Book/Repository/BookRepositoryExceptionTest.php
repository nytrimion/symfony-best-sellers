<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Book\Repository;

use App\Domain\Book\Repository\BookRepositoryException;
use PHPUnit\Framework\TestCase;

final class BookRepositoryExceptionTest extends TestCase
{
    public function testHasFailedToFetchResource(): void
    {
        $previous = new \Exception();
        $sut = BookRepositoryException::hasFailedToFetchResource('whatever', 42, $previous);

        $this->assertSame('Book Repository has failed to fetch the resource: whatever', $sut->getMessage());
        $this->assertSame(42, $sut->getCode());
        $this->assertSame($previous, $sut->getPrevious());
    }
}
