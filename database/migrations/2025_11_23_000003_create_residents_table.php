<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table): void {
            $table->id();
            $table->uuid('reference_id')->unique();
            $table->foreignId('household_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('civil_status', 30)->nullable();
            $table->string('occupation')->nullable();
            $table->string('religion')->nullable();
            $table->unsignedTinyInteger('years_of_residency')->default(0);
            $table->string('residency_status')->default('active');
            $table->boolean('is_voter')->default(false);
            $table->string('voter_precinct')->nullable();
            $table->text('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address_line')->nullable();
            $table->string('purok')->nullable();
            $table->string('education')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
            $table->index(['residency_status', 'purok']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
