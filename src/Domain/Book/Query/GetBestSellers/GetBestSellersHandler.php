<?php

declare(strict_types=1);

namespace App\Domain\Book\Query\GetBestSellers;

use App\Domain\Book\Repository\BookRepository;
use App\Domain\Book\Repository\BookRepositoryException;

final readonly class GetBestSellersHandler
{
    public function __construct(
        private BookRepository $bookRepository,
    ) {}

    /**
     * @throws BookRepositoryException
     */
    public function handle(GetBestSellersQuery $query): GetBestSellersResponse
    {
        return new GetBestSellersResponse(
            $this->bookRepository->getBestSellers($query),
        );
    }
}
