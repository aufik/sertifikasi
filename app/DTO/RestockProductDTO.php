<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class RestockProductDTO
{
    public function __construct(public int $quantity, public ?string $reason = null) {}

    /** @return array{quantity: int, reason: string|null} */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'reason' => $this->reason,
        ];
    }
}
