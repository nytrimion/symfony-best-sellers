<?php

declare(strict_types=1);

namespace App\Infrastructure\Book\Repository;

use App\Domain\Book\Query\GetBestSellers\GetBestSellersQuery;
use App\Domain\Book\Repository\BookRepository;
use App\Domain\Book\Repository\BookRepositoryException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class NewYorkTimesBookRepository implements BookRepository
{
    private const string BEST_SELLERS_ENDPOINT = '/books/v3/lists/best-sellers/history.json';
    private const string API_KEY_PARAM = 'api-key';

    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl,
        private string $apiKey,
    ) {}

    public function getBestSellers(GetBestSellersQuery $query): array
    {
        $params = array_merge((array) $query, [
            'isbn' => implode(';', $query->isbn),
        ]);

        return $this->getJson(self::BEST_SELLERS_ENDPOINT, $params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     * @throws BookRepositoryException
     */
    private function getJson(string $endpoint, array $params): array
    {
        $url = $this->getEndpointUrl($endpoint);
        $queryParams = $this->createQueryParams($params);
        $options = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => $queryParams,
        ];

        try {
            return $this->client->request('GET', $url, $options)->toArray();
        } catch (\Throwable $throwable) {
            throw BookRepositoryException::hasFailedToFetchResource(
                description: rtrim($url . '?' . http_build_query($queryParams), '?'),
                code: $throwable->getCode(),
                previous: $throwable,
            );
        }
    }

    private function getEndpointUrl(string $endpoint): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->apiUrl, '/'),
            ltrim($endpoint, '/'),
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function createQueryParams(array $params): array
    {
        $params = array_merge($params, [
            self::API_KEY_PARAM => $this->apiKey,
        ]);

        return $this->sanitizeParams($params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function sanitizeParams(array $params): array
    {
        return array_filter(
            $params,
            static fn(mixed $value): bool => !empty($value),
        );
    }
}