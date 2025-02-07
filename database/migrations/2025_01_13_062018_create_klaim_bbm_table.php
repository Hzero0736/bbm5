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
            $table->string('no_acc');
            $table->string('periode'); // Format: YYYY-MM
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kendaraan_id')->constrained('kendaraan');
            $table->foreignId('bbm_id')->constrained('bbm');
            $table->decimal('jumlah_dana', 10, 2);
            $table->decimal('saldo_liter', 8, 2)->default(200.00);
            $table->decimal('total_penggunaan_liter', 8, 2)->default(0);
            $table->decimal('sisa_saldo_liter', 8, 2)->default(200.00);
            $table->timestamps();
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
