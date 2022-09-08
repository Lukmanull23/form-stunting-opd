<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bappelitbang extends Model
{
    use HasFactory;

    protected $table = 'opd_bappelitbang';

    protected $fillable = [
        'tahun',
        'bulan',
        'terselenggara_rembuk_stunting_t_kb_ka',
        'keterangan_terselenggara_rembuk_stunting_t_kb_ka',
        'terselenggara_rembuk_stunting_t_kct',
        'keterangan_terselenggara_rembuk_stunting_t_kct',
        'tersedia_kebijakan_dlm_penurunan_stunting',
        'keterangan_tersedia_kebijakan_dlm_penurunan_stunting',
        'terselenggara_pemantuan_eval_percepatan_penurunan_stunting',
        'keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting',
    ];
}
