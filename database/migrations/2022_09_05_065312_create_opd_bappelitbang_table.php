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
        Schema::create('opd_bappelitbang', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->tinyInteger('bulan');

            /**
             * Selebihnya disesuaikan seperti di excel, jangan diketik terlalu panjang
             * Untuk timestamps biarkan di bagian terakhir
             * 
             * Contoh:
             * $table->integer('publikasi_data_stunting_kab_kota');
             */

            $table->string('terselenggara_rembuk_stunting_t_kb_ka')->nullable();
            $table->string('keterangan_terselenggara_rembuk_stunting_t_kb_ka')->nullable();
            $table->string('terselenggara_rembuk_stunting_t_kct')->nullable();
            $table->string('keterangan_terselenggara_rembuk_stunting_t_kct')->nullable();
            $table->string('tersedia_kebijakan_dlm_penurunan_stunting')->nullable();
            $table->string('keterangan_tersedia_kebijakan_dlm_penurunan_stunting')->nullable();
            $table->string('terselenggara_pemantuan_eval_percepatan_penurunan_stunting')->nullable();
            $table->string('keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting')->nullable();
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
        Schema::dropIfExists('opd_bappelitbang');
    }
};
