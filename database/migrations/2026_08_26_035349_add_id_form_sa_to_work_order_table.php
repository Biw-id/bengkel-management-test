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
        Schema::table('work_order', function (Blueprint $table) {
            $table->string('id_form_sa')->nullable()->after('id_wo');
            $table->foreign('id_form_sa')->references('id_form_sa')->on('form_service_advisor')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->dropForeign(['id_form_sa']);
            $table->dropColumn('id_form_sa');
        });
    }
};
