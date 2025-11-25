<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('verification_status')->default('pending')->after('is_active');
            $table->string('verification_proof_path')->nullable()->after('verification_status');
            $table->text('verification_notes')->nullable()->after('verification_proof_path');
            $table->foreignId('verified_by')->nullable()->after('verification_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verification_proof_path',
                'verification_notes',
                'verified_by',
                'verified_at',
            ]);
        });
    }
};
