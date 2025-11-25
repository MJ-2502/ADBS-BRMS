<?php

use App\Enums\CertificateStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('certificate_type');
            $table->string('purpose');
            $table->string('status')->default(CertificateStatus::Pending->value);
            $table->text('remarks')->nullable();
            $table->json('payload')->nullable();
            $table->string('reference_no')->unique();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index(['certificate_type', 'status']);
            $table->index(['resident_id', 'requested_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
