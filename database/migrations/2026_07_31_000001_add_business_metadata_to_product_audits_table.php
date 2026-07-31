<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_audits', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->string('user_name')->nullable()->after('user_id');
            $table->string('user_email')->nullable()->index()->after('user_name');
            $table->string('ip_address', 45)->nullable()->after('request_id');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('method', 10)->nullable()->after('user_agent');
            $table->string('path')->nullable()->after('method');
        });
    }

    public function down(): void
    {
        Schema::table('product_audits', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['user_name', 'user_email', 'ip_address', 'user_agent', 'method', 'path']);
        });
    }
};
