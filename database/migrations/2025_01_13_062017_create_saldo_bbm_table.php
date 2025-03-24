<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_bbm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('periode', 7)->comment('Format: YYYY-MM'); // Gunakan string dengan panjang tetap
            $table->decimal('saldo_awal', 8, 2)->default(200.00);
            $table->decimal('total_penggunaan', 8, 2)->default(0);
            $table->decimal('sisa_saldo', 8, 2)->default(200.00);
            $table->enum('status', ['normal', 'melebihi_batas'])->default('normal');
            $table->timestamps();

            // Unique constraint untuk user dan periode
            $table->unique(['user_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_bbm');
    }
};
