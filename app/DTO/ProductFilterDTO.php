<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $search,
        public ?string $status,
        public ?string $categoryId,
        public int $perPage = 15,
        public string $sort = 'latest'
    ) {}

    public static function fromArray(array $data): self
    {
        $perPage = max(1, min((int) ($data['per_page'] ?? 15), 100));

        return new self(
            $data['search'] ?? null,
            $data['status'] ?? null,
            $data['category_id'] ?? null,
            $perPage,
            (string) ($data['sort'] ?? 'latest')
        );
    }
}
