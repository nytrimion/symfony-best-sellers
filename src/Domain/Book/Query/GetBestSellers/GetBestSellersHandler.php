<?php

declare(strict_types=1);

namespace App\Domain\Book\Query\GetBestSellers;

use App\Domain\Book\Dto\BestSellersQuery;
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
        $query = new BestSellersQuery(
            author: trim($query->author),
            title: trim($query->title),
            isbn: array_values(array_unique($query->isbn)),
            offset: $query->offset,
        );
        $response = $this->bookRepository->getBestSellers($query);

        return new GetBestSellersResponse($response->json);
    }
}
