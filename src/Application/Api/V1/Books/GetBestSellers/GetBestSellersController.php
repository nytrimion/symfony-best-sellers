<?php

declare(strict_types=1);

namespace App\Application\Api\V1\Books\GetBestSellers;

use App\Domain\Book\Query\GetBestSellers\GetBestSellersHandler;
use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/best-sellers', name: 'best_sellers', methods: ['GET'])]
final class GetBestSellersController extends AbstractController
{
    public function __construct(
        private GetBestSellersHandler $handler,
    ) {}

    public function __invoke(
        #[MapQueryString(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
        )]
        GetBestSellersQueryParams $queryParams,
    ): JsonResponse
    {
        $response = $this->handler->handle(new GetBestSellersQuery(
            author: $queryParams->author ?? '',
            title: $queryParams->title ?? '',
            isbn: $queryParams->isbn ?? [],
            offset: $queryParams->offset ?? 0,
        ));

        return new JsonResponse($response->json);
    }
}
