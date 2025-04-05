<?php

declare(strict_types=1);

namespace App\Domain\Book\Dto;

final readonly class BestSellersQuery
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
