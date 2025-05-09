<?php

declare(strict_types=1);

namespace App\Domain\Book\Query\GetBestSellers;

final readonly class GetBestSellersResponse
{
    /**
     * @param array<string, mixed> $json
     */
    public function __construct(
        public array $json,
    ) {}
}
