<?php

declare(strict_types=1);

namespace App\Domain\Book\Query\GetBestSellers;

final readonly class GetBestSellersQuery
{
    public string $author;
    public string $title;
    /** @param string[] $isbn */
    public array $isbn;
    public int $offset;

    /**
     * @param string[] $isbn
     */
    public function __construct(
        string $author = '',
        string $title = '',
        array $isbn = [],
        int $offset = 0,
    ) {
        $this->author = trim($author);
        $this->title = trim($title);
        $this->isbn = array_values(array_unique($isbn));
        $this->offset = $offset;
    }
}
