<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_klaim_bbm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klaim_bbm_id')->constrained('klaim_bbm')->onDelete('cascade');
            $table->string('periode');
            $table->date('tanggal');
            $table->integer('km');
            $table->foreignId('bbm_id')->constrained('bbm');
            $table->decimal('liter', 10, 2);
            $table->decimal('total_harga', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_klaim_bbm');
    }
};
