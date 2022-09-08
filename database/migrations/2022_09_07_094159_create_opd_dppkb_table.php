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
        Schema::create('opd_dppkb', function (Blueprint $table) {
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
            //kb1supply
            $table->string('tersedia_data_hasil_surveilans_KBS')->nullable();
            $table->string('keterangan_tersedia_data_hasil_surveilans_KBS')->nullable();
            $table->string('tersedia_data_KBS_melalui_SI')->nullable();
            $table->string('keterangan_tersedia_data_KBS_melalui_SI')->nullable();
            $table->string('persentase_terima_pendamping_penurunan_stunting')->nullable();
            $table->string('ket_persentase_terima_pendamping_penurunan_stunting')->nullable();
            //datasasaran
            $table->integer('jumlah_keluarga')->nullable();
            $table->integer('jumlah_keluarga_beresiko_stunting')->nullable();
            $table->double('persentase', 5, 2)->nullable();
            //bumil3
            $table->integer('jml_PUS_belum_terpenuhi_kebutuhan_KB')->nullable();
            $table->integer('jml_PUS_keseluruhan')->nullable();
            $table->double('persentase_PUS_tidak_terlayani', 5, 2)->nullable();
            //bumil4
            $table->integer('jml_kehamilan_tidak_diinginkan_KTD')->nullable();
            $table->integer('jml_ibu_hamil')->nullable();
            $table->double('persentase_KTD_kehamilan_belum_direncanakan', 5, 2)->nullable();
            //kelberesiko4
            $table->integer('Jml_ibu_pasca_bersalin_mendapat_pelayanan')->nullable();
            $table->integer('jml_seluruh_ibu_pasca_persalinan')->nullable();
            $table->double('persentase_layanan_KB_kurun_waktu_sama', 5, 2)->nullable();
            //kelberesiko5
            $table->integer('jml_keluarga_berisiko_stunting_memperoleh_pendampingan')->nullable();
            $table->integer('jml_seluruh_keluarga_berisiko')->nullable();
            $table->double('persentase_layanan_pendampingan_keluagra_berisiko', 5, 2)->nullable();
            //Okpus3
            $table->integer('calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi')->nullable();
            $table->integer('jml_PUS_mendaftarkan_pernikahan')->nullable();
            $table->double('cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi', 5, 2)->nullable();

            //kb2supply
            $table->integer('PIK_dan_BKR_edukasi')->nullable();
            $table->integer('kelurahan_melaksanakan_BKB')->nullable();
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
        Schema::dropIfExists('opd_dppkb');
    }
};
