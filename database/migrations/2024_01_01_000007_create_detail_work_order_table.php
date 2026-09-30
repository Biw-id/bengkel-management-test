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
        Schema::create('detail_work_order', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('id_wo')->index();
            $table->string('jenis_penanganan');
            $table->integer('biaya');
            $table->text('deskripsi')->nullable();
            $table->integer('qty')->default(1);
            $table->timestamps();

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
        Schema::dropIfExists('detail_work_order');
    }
};
