<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_bpom', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->tinyInteger('bulan');
            $table->foreignId('kelurahan')
                  ->nullable()
                  ->constrained('master_kelurahan')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            /**
             * Selebihnya disesuaikan seperti di excel, jangan diketik terlalu panjang
             * Untuk timestamps biarkan di bagian terakhir
             * 
             * Contoh:
             * $table->integer('publikasi_data_stunting_kab_kota');
             */
            $table->string('pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha')->nullable();
            $table->string('keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_bpom');
    }
};