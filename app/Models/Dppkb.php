<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dppkb extends Model
{
    use HasFactory;

    protected $table = 'opd_Dppkb';

    protected $fillable = [
        'tahun',
        'bulan',
        'kelurahan',
        // Sesuaikan dengan yg dideklarasikan di migration
        //kb1supply
        'tersedia_data_hasil_surveilans_KBS',
        'keterangan_tersedia_data_hasil_surveilans_KBS',
        'tersedia_data_KBS_melalui_SI',
        'keterangan_tersedia_data_KBS_melalui_SI',
        'persentase_terima_pendamping_penurunan_stunting',
        'ket_persentase_terima_pendamping_penurunan_stunting',
         //datasasaran
        'jumlah_keluarga',
        'jumlah_keluarga_beresiko_stunting',
        'persentase',
        //bumil3
        'jml_PUS_belum_terpenuhi_kebutuhan_KB',
        'jml_PUS_keseluruhan',
        'persentase_PUS_tidak_terlayani',
        //bumil4
        'jml_kehamilan_tidak_diinginkan_KTD',
        'jml_ibu_hamil',
        'persentase_KTD_kehamilan_belum_direncanakan',
        //kelberesiko4
        'Jml_ibu_pasca_bersalin_mendapat_pelayanan',
        'jml_seluruh_ibu_pasca_persalinan',
        'persentase_layanan_KB_kurun_waktu_sama',
        //kelberesiko5
        'jml_keluarga_berisiko_stunting_memperoleh_pendampingan',
        'jml_seluruh_keluarga_berisiko',
        'persentase_layanan_pendampingan_keluagra_berisiko',
        //Okpus3
        'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi',
        'jml_PUS_mendaftarkan_pernikahan',
        'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi',

        //kb2supply
        'PIK_dan_BKR_edukasi',
        'kelurahan_melaksanakan_BKB',
    ];
    
}
