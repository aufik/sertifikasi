<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTO\CreateProductDTO;
use App\DTO\ProductFilterDTO;
use App\DTO\RestockProductDTO;
use App\DTO\UpdateProductDTO;
use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductPurged;
use App\Events\ProductRestocked;
use App\Events\ProductRestored;
use App\Events\ProductUpdated;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ProductService extends BaseService
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    /** Ambil daftar produk sesuai filter; log teknisnya otomatis dicatat oleh middleware. */
    public function list(ProductFilterDTO $filters): LengthAwarePaginator
    {
        return $this->products->search($filters);
    }

    /** Buat produk baru di dalam transaksi database dan kirim event ProductCreated untuk audit bisnis. */
    public function create(CreateProductDTO $dto): Product
    {
        return DB::transaction(function () use ($dto): Product {
            $product = $this->products->create($dto->toArray());
            event(new ProductCreated($product));
            Cache::flush();

            return $product;
        });
    }

    /** Ubah produk dengan lock database supaya data stok tidak bentrok saat diakses bersamaan. */
    public function update(string $id, UpdateProductDTO $dto): Product
    {
        return DB::transaction(function () use ($id, $dto): Product {
            $product = $this->products->findWithLock($id);
            $product->update($dto->toArray());
            event(new ProductUpdated($product->refresh()));
            Cache::flush();

            return $product;
        });
    }

    /** Soft delete produk dan kirim event ProductDeleted supaya terlihat di log bisnis dashboard. */
    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $product = $this->products->findWithLock($id);
            $deleted = (bool) $product->delete();
            event(new ProductDeleted($product));
            Cache::flush();

            return $deleted;
        });
    }

    /** Tambah stok secara aman, lalu simpan stok lama dan stok baru sebagai bukti audit restock. */
    public function restock(string $id, RestockProductDTO $dto): Product
    {
        return DB::transaction(function () use ($id, $dto): Product {
            $product = $this->products->findWithLock($id);
            $oldStock = $product->stock;
            $product->stock = $oldStock + $dto->quantity;
            $product->save();
            $product->refresh();

            $context = [
                'old_stock' => $oldStock,
                'added_quantity' => $dto->quantity,
                'new_stock' => $product->stock,
                'reason' => $dto->reason,
            ];

            event(new ProductRestocked($product, $context));
            Cache::flush();

            return $product;
        });
    }

    /** Pulihkan produk yang sudah soft delete dan catat event ProductRestored. */
    public function restore(string $id): Product
    {
        return DB::transaction(function () use ($id): Product {
            $product = Product::withTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            $product->restore();
            $product->refresh();
            event(new ProductRestored($product));
            Cache::flush();

            return $product;
        });
    }

    /** Hapus permanen produk dari trash setelah event ProductPurged dikirim untuk audit. */
    public function purge(string $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $product = Product::withTrashed()->whereKey($id)->lockForUpdate()->firstOrFail();
            event(new ProductPurged($product));
            Cache::flush();

            return (bool) $product->forceDelete();
        });
    }
}
