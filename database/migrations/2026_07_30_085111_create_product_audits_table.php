<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_audits', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('product_id')->nullable()->index();
            $table->string('event')->index();
            $table->json('payload')->nullable();
            $table->uuid('request_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_audits');
    }
};
