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
        Schema::create('opd_kemenag', function (Blueprint $table) {
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
            $table->string('terlaksana_kampanye_pencegahan_stunting')->nullable();
            $table->string('keterangan_terlaksana_kampanye_pencegahan_stunting')->nullable();
            $table->integer('calon_pasangan_usia_subur_memperoleh_pendampingan')->nullable();
            $table->integer('jumlah_pasangan_usia_subur_mendaftarkan_pernikahan')->nullable();
            $table->double('persentase_layanan_bimbingan_perkawinan_materi_stunting', 5, 2)->nullable();
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
        Schema::dropIfExists('opd_kemenag');
    }
};
