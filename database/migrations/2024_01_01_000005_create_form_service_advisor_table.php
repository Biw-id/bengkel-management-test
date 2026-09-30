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
        Schema::create('form_service_advisor', function (Blueprint $table) {
            $table->string('id_form_sa')->primary();
            $table->string('id_customer')->index();
            $table->timestamp('waktu_form_sa');
            $table->text('keluhan_konsumen');
            $table->string('no_plat');
            $table->timestamps();

            $table->foreign('id_customer')
                ->references('id_customer')
                ->on('master_customer')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_service_advisor');
    }
};
