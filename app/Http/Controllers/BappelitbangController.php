<?php

namespace App\Http\Controllers;

use App\Models\Bappelitbang;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BappelitbangController extends Controller
{
    private $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $report_history = Bappelitbang::select(DB::raw('MAX(id), tahun, bulan'))->groupBy('bulan', 'tahun')->get();
        extract(get_object_vars($this));

        return view('pages.Bappelitbang', compact('report_history', 'months'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validasi kolom input yang akan diproses
         */
        //dd($request->input());
        $validation = $request->validate([
            'date' => 'required',
            'terselenggara_rembuk_stunting_t_kb_ka' => 'required',
            'keterangan_terselenggara_rembuk_stunting_t_kb_ka' => 'nullable|string',
            'terselenggara_rembuk_stunting_t_kct' => 'required',
            'keterangan_terselenggara_rembuk_stunting_t_kct' => 'nullable|string',
            'tersedia_kebijakan_dlm_penurunan_stunting' => 'required',
            'keterangan_tersedia_kebijakan_dlm_penurunan_stunting' => 'nullable|string',
            'terselenggara_pemantuan_eval_percepatan_penurunan_stunting' => 'required',
            'keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting' => 'nullable|string',
        ]);
        /**
         * Jika validasi gagal, kembali ke halaman sebelumnya dengan pesan error
         */
        if (!$validation) return back();
        /**
         * Pisahkan tahun dan bulan
         */
        $date = explode('-', $request->date);
        $tahun = $date[0];
        $bulan = $date[1];

        /**
         * Cek apakah ada data pada tahun dan bulan (periode) yang sama, jika ada maka data tidak dapat disimpan untuk menghindari data duplikat
         * Lalu kembali ke halaman sebelumnya dengan pesan error
         */
        $existing_periode = Bappelitbang::where('tahun', $tahun)->where('bulan', $bulan)->first();
        if ($existing_periode) {
            return back()->with('error', "Data pada periode yang sama ({$this->months[$bulan - 1]} {$tahun}) sudah ada, tidak dapat menyimpan data duplikat.")->withInput();
        }

        /**
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Diskominfo yaitu data pada sheet "Kominfo"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'terselenggara_rembuk_stunting_t_kb_ka' => $request->terselenggara_rembuk_stunting_t_kb_ka,
            'keterangan_terselenggara_rembuk_stunting_t_kb_ka' => $request->keterangan_terselenggara_rembuk_stunting_t_kb_ka,
            'terselenggara_rembuk_stunting_t_kct' => $request->terselenggara_rembuk_stunting_t_kct,
            'keterangan_terselenggara_rembuk_stunting_t_kct' => $request->keterangan_terselenggara_rembuk_stunting_t_kct,
            'tersedia_kebijakan_dlm_penurunan_stunting' => $request->tersedia_kebijakan_dlm_penurunan_stunting,
            'keterangan_tersedia_kebijakan_dlm_penurunan_stunting' => $request->keterangan_tersedia_kebijakan_dlm_penurunan_stunting,
            'terselenggara_pemantuan_eval_percepatan_penurunan_stunting' => $request->terselenggara_pemantuan_eval_percepatan_penurunan_stunting,
            'keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting' => $request->keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting
        ];

        /**
         * Submit data yang sudah disiapkan
         * Untuk data per kelurahan menggunakan perintah upsert untuk batch insert
         */


        $non_kelurahan_insert = Bappelitbang::create($non_kelurahan_data);

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data) return redirect('/form/bappelitbang')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bappelitbang  $bappelitbang
     * @return \Illuminate\Http\Response
     */
    public function edit(Bappelitbang $bappelitbang, $params)
    {
        $periods = explode("-", $params);
        $report_non_kelurahan = Bappelitbang::where('bulan', $periods[1])->where('tahun', $periods[0])->first();
        return view('pages.bappelitbang-edit', compact('report_non_kelurahan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bappelitbang  $bappelitbang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bappelitbang $bappelitbang)
    {
        /**
         * Validasi kolom yang akan diproses
         */
        $validation = $request->validate([
            'date' => 'required',
            'terselenggara_rembuk_stunting_t_kb_ka' => 'required',
            'keterangan_terselenggara_rembuk_stunting_t_kb_ka' => 'nullable|string',
            'terselenggara_rembuk_stunting_t_kct' => 'required',
            'keterangan_terselenggara_rembuk_stunting_t_kct' => 'nullable|string',
            'tersedia_kebijakan_dlm_penurunan_stunting' => 'required',
            'keterangan_tersedia_kebijakan_dlm_penurunan_stunting' => 'nullable|string',
            'terselenggara_pemantuan_eval_percepatan_penurunan_stunting' => 'required',
            'keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting' => 'nullable|string',
        ]);
        /**
         * Jika validasi gagal, kembali ke halaman sebelumnya dengan pesan error
         */
        if (!$validation) return back();

        /**
         * Pisahkan tahun dan bulan
         */
        $date = explode('-', $request->date);
        $tahun = $date[0];
        $bulan = $date[1];

        /**
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Diskominfo yaitu data pada sheet "Kominfo"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'terselenggara_rembuk_stunting_t_kb_ka' => $request->terselenggara_rembuk_stunting_t_kb_ka,
            'keterangan_terselenggara_rembuk_stunting_t_kb_ka' => $request->keterangan_terselenggara_rembuk_stunting_t_kb_ka,
            'terselenggara_rembuk_stunting_t_kct' => $request->terselenggara_rembuk_stunting_t_kct,
            'keterangan_terselenggara_rembuk_stunting_t_kct' => $request->keterangan_terselenggara_rembuk_stunting_t_kct,
            'tersedia_kebijakan_dlm_penurunan_stunting' => $request->tersedia_kebijakan_dlm_penurunan_stunting,
            'keterangan_tersedia_kebijakan_dlm_penurunan_stunting' => $request->keterangan_tersedia_kebijakan_dlm_penurunan_stunting,
            'terselenggara_pemantuan_eval_percepatan_penurunan_stunting' => $request->terselenggara_pemantuan_eval_percepatan_penurunan_stunting,
            'keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting' => $request->keterangan_terselenggara_pemantuan_eval_pcpt_penurunan_stunting
        ];

        /**
         * Update data yang sudah disiapkan
         * Untuk data non kelurahan (kolom isian ya/tidak) menggunakan perintah where lalu update
         * Parameter id_report_non_kelurahan dapat dilihat pada form edit, silakan cek form edit dan cari textfield "id_report_non_kelurahan"
         * (silakan isikan sesuai dengan kolom pada OPD yang kalian kerjakan, kolom yg sama untuk semua OPD hanya tahun dan bulan)
         */

        $non_kelurahan_insert = Bappelitbang::where('id', $request->id_report_non_kelurahan)->update($non_kelurahan_data);

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data) return redirect('/form/bappelitbang')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }
}