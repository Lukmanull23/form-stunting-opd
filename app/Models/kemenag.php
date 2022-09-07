<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kemenag extends Model
{
    use HasFactory;

    protected $table = 'opd_kemenag';

    protected $fillable = [
        'tahun',
        'bulan',
        'kelurahan',
        // Sesuaikan dengan yg dideklarasikan di migration
        'terlaksana_kampanye_pencegahan_stunting',
        'keterangan_terlaksana_kampanye_pencegahan_stunting',
        'calon_pasangan_usia_subur_memperoleh_pendampingan',
        'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan',
        'persentase_layanan_bimbingan_perkawinan_materi_stunting'
        
    ];
    
}
