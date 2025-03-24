<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('klaim_bbm', function (Blueprint $table) {
            $table->id();
            $table->string('klaim_id', 20)->unique()->comment('Format: KLM-YYYYMMDD-XXXX');
            $table->string('no_acc', 20);
            $table->string('periode', 7)->comment('Format: YYYY-MM'); // Gunakan string dengan panjang tetap
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->foreignId('bbm_id')->constrained('bbm')->onDelete('cascade');
            $table->foreignId('saldo_bbm_id')->nullable()->constrained('saldo_bbm')->onDelete('set null');
            $table->decimal('jumlah_dana', 12, 2)->default(0);
            $table->decimal('total_penggunaan_liter', 8, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index untuk mempercepat pencarian
            $table->index(['user_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaim_bbm');
    }
};
