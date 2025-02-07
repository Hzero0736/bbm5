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
        Schema::create('bbm', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bbm');
            $table->decimal('harga_bbm', 10, 2);
            $table->string('satuan_bbm')->default('Liter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbm');
    }
};
