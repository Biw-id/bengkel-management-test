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
        Schema::create('nota_jasa_bengkel', function (Blueprint $table) {
            $table->string('id_njb')->primary();
            $table->string('id_wo')->index();
            $table->timestamp('waktu_njb');
            $table->string('id_mekanik')->index();
            $table->string('tipe_unit');
            $table->timestamps();

            $table->foreign('id_wo')
                ->references('id_wo')
                ->on('work_order')
                ->onDelete('restrict')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('nota_jasa_bengkel');
    }
};
