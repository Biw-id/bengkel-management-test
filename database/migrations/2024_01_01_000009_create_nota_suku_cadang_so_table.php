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
        Schema::create('nota_suku_cadang_so', function (Blueprint $table) {
            $table->string('no_nsc')->primary();
            $table->timestamp('tgl_nsc');
            $table->string('id_customer')->index();
            $table->integer('amount')->default(0);
            $table->string('id_wo')->index();
            $table->integer('diskon_part')->default(0);
            $table->integer('total_amount')->default(0);
            $table->timestamps();

            $table->foreign('id_customer')
                ->references('id_customer')
                ->on('master_customer')
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
        Schema::dropIfExists('nota_suku_cadang_so');
    }
};
