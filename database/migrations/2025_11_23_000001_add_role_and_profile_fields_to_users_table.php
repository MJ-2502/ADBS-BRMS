<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default(UserRole::Resident->value)->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('purok')->nullable()->after('phone');
            $table->string('address_line')->nullable()->after('purok');
            $table->string('api_token', 80)->nullable()->unique()->after('address_line');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            $table->json('preferences')->nullable()->after('last_login_at');

            $table->index('role');
            $table->index(['is_active', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'role',
                'phone',
                'purok',
                'address_line',
                'api_token',
                'is_active',
                'last_login_at',
                'preferences',
            ]);
        });
    }
};
