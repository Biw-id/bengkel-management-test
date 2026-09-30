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
        Schema::create('work_order', function (Blueprint $table) {
            $table->string('id_wo')->primary();
            $table->timestamp('waktu_wo');
            $table->enum('status_wo', ['PENDING', 'PROSES', 'SELESAI', 'CANCEL'])->default('PENDING');
            $table->string('id_mekanik')->index();
            $table->integer('total_bayar_jasa')->default(0);
            $table->integer('total_bayar_part')->default(0);
            $table->integer('total_bayar_keseluruhan')->default(0);
            $table->timestamps();

            $table->foreign('id_mekanik')
                ->references('honda_id_mekanik')
                ->on('master_mekanik')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};
