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
        Schema::create('master_part', function (Blueprint $table) {
            $table->string('part_number')->primary();
            $table->string('nama_part');
            $table->integer('harga');
            $table->integer('qty_stock')->default(0);
            $table->integer('qty_rfs')->default(0);
            $table->integer('qty_book')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_part');
    }
};
