<?php

use App\Enums\CertificateType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_fees', function (Blueprint $table): void {
            $table->id();
            $table->string('certificate_type')->unique();
            $table->decimal('amount', 8, 2)->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = collect(CertificateType::cases())->map(fn (CertificateType $type) => [
            'certificate_type' => $type->value,
            'amount' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::table('certificate_fees')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_fees');
    }
};
