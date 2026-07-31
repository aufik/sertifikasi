<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

final class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'viewer'], true);
    }

    public function view(User $user, Product $product): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager'], true);
    }

    public function update(User $user, Product $product): bool
    {
        return in_array($user->role, ['admin', 'manager'], true);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    public function restock(User $user, Product $product): bool
    {
        return in_array($user->role, ['admin', 'manager'], true);
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }
}
