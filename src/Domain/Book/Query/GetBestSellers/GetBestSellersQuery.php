<?php

declare(strict_types=1);

namespace App\Domain\Book\Query\GetBestSellers;

final readonly class GetBestSellersQuery
{
    /**
     * @param string[] $isbn
     */
    public function __construct(
        public string $author = '',
        public string $title = '',
        public array $isbn = [],
        public int $offset = 0,
    ) {}
}
