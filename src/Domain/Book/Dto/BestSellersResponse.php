<?php

declare(strict_types=1);

namespace App\Domain\Book\Dto;

final readonly class BestSellersResponse
{
    /**
     * @param array<string, mixed> $json
     */
    public function __construct(
        public array $json,
    ) {}
}
