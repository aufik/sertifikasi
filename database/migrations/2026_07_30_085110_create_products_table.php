<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->index();
            $table->json('metadata')->nullable();
            $table->text('search_vector')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['category_id', 'status']);
            $table->index(['status', 'created_at']);
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX products_search_vector_gin ON products USING GIN (to_tsvector('simple', coalesce(name,'') || ' ' || coalesce(description,'') || ' ' || coalesce(sku,'')))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
