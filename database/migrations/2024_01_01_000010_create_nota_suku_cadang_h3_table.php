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
        Schema::create('nota_suku_cadang_h3', function (Blueprint $table) {
            $table->id();
            $table->string('no_nsc')->index();
            $table->string('part_number')->index();
            $table->integer('qty')->default(1);
            $table->integer('harga');
            $table->integer('jumlah_bayar');
            $table->string('id_wo')->index();
            $table->timestamps();

            $table->foreign('no_nsc')
                ->references('no_nsc')
                ->on('nota_suku_cadang_so')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('part_number')
                ->references('part_number')
                ->on('master_part')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_wo')
                ->references('id_wo')
                ->on('work_order')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_suku_cadang_h3');
    }
};
