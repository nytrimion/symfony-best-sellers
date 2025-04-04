<?php

declare(strict_types=1);

namespace App\Domain\Book\Repository;

use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;

interface BookRepository
{
    /**
     * @return array<string, mixed>
     * @throws BookRepositoryException
     */
    public function getBestSellers(GetBestSellersQuery $query): array;
}
