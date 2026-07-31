<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

final class ProductAudit extends Model
{
    use HasUlids;

    protected $fillable = ['product_id', 'user_id', 'user_name', 'user_email', 'event', 'payload', 'request_id', 'ip_address', 'user_agent', 'method', 'path'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
