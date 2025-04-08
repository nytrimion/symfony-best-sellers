<?php

declare(strict_types=1);

namespace App\Application\Api\V1\Books\GetBestSellers;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetBestSellersQueryParams
{
    public function __construct(
        public ?string $author,
        #[Assert\Type('list')]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Isbn(),
        ])]
        public ?array $isbn,
        public ?string $title,
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\DivisibleBy(20)]
        public ?int $offset,
    ) {}
}
