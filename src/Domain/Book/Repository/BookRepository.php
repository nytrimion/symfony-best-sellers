<?php

declare(strict_types=1);

namespace App\Domain\Book\Repository;

use App\Domain\Book\Dto\BestSellersQuery;
use App\Domain\Book\Dto\BestSellersResponse;

interface BookRepository
{
    /**
     * @throws BookRepositoryException
     */
    public function getBestSellers(BestSellersQuery $query): BestSellersResponse;
}
