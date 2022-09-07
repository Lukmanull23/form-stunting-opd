<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpom extends Model
{
    use HasFactory;

    protected $table = 'opd_bpom';

    protected $fillable = [
        'tahun',
        'bulan',
        'pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha',
        'keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha',

    ];
}
