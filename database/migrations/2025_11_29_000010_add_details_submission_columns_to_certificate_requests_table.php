<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table): void {
            $table->foreignId('details_submitted_by')
                ->nullable()
                ->after('approved_by')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('details_submitted_at')->nullable()->after('details_submitted_by');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('details_submitted_by');
            $table->dropColumn('details_submitted_at');
        });
    }
};
