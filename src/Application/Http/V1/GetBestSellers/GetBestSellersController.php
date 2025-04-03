<?php

declare(strict_types=1);

namespace App\Application\Http\V1\GetBestSellers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class GetBestSellersController
{
    #[Route('/best-sellers', name: 'best_sellers')]
    public function __invoke(
        #[MapQueryString(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
        )]
        GetBestSellersRequest $getBestSellersRequest,
    ): JsonResponse
    {
        return new JsonResponse(['status' => 'OK']);
    }
}
