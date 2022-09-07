<?php

namespace App\Http\Controllers;

use App\Models\Kemenag;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KemenagController extends Controller
{
    private $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelurahan = Kelurahan::all();
        $report_history = Kemenag::select(DB::raw('MAX(id), tahun, bulan'))->groupBy('bulan', 'tahun')->get();
        extract(get_object_vars($this));

        return view('pages.kemenag', compact('kelurahan', 'report_history', 'months'));
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
        $validation = $request->validate([
            'date' => 'required',
            'terlaksana_kampanye_pencegahan_stunting' => 'required',
            'keterangan_terlaksana_kampanye_pencegahan_stunting' => 'nullable|string',
            'kelurahan' => 'required',
            'kelurahan.*' => 'sometimes|numeric',
            'calon_pasangan_usia_subur_memperoleh_pendampingan' => 'required',
            'calon_pasangan_usia_subur_memperoleh_pendampingan.*' => 'sometimes|numeric',
            'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan' => 'required',
            'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan.*' => 'sometimes|numeric',
            'persentase_layanan_bimbingan_perkawinan_materi_stunting' => 'required',
            'persentase_layanan_bimbingan_perkawinan_materi_stunting.*' => 'sometimes|numeric',
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
        $existing_periode = Kemenag::where('tahun', $tahun)->where('bulan', $bulan)->first();
        if ($existing_periode) {
            return back()->with('error', "Data pada periode yang sama ({$this->months[$bulan - 1]} {$tahun}) sudah ada, tidak dapat menyimpan data duplikat.")->withInput();
        }

        /**
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Kemenag yaitu data pada sheet "Kominfo"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'terlaksana_kampanye_pencegahan_stunting' => $request->terlaksana_kampanye_pencegahan_stunting,
            'keterangan_terlaksana_kampanye_pencegahan_stunting' => $request->keterangan_terlaksana_kampanye_pencegahan_stunting
        ];

        /**
         * Siapkan data yang berbentuk per kelurahan, contohnya pada OPD Kemenag yaitu data pada sheet "Kesehatan (Data Supply)"
         */
        $per_kelurahan_data = [];
        for ($i = 0; $i < count($request->calon_pasangan_usia_subur_memperoleh_pendampingan); $i++) {
            array_push($per_kelurahan_data, [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kelurahan' => $request->kelurahan[$i],
                'terlaksana_kampanye_pencegahan_stunting' => null,
                'keterangan_terlaksana_kampanye_pencegahan_stunting' => null,
                'calon_pasangan_usia_subur_memperoleh_pendampingan' => $request->calon_pasangan_usia_subur_memperoleh_pendampingan[$i],
                'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan' => $request->jumlah_pasangan_usia_subur_mendaftarkan_pernikahan[$i],
                'persentase_layanan_bimbingan_perkawinan_materi_stunting' => $request->persentase_layanan_bimbingan_perkawinan_materi_stunting[$i],
            ]);
        }

        /**
         * Submit data yang sudah disiapkan
         * Untuk data per kelurahan menggunakan perintah upsert untuk batch insert
         */
        $non_kelurahan_insert = Kemenag::create($non_kelurahan_data);
        $per_kelurahan_insert = Kemenag::upsert($per_kelurahan_data, []);

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data && $per_kelurahan_data) return redirect('/form/kemenag')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kemenag  $kemenag
     * @return \Illuminate\Http\Response
     */
    public function edit(Kemenag $kemenag, $params)
    {
        $periods = explode("-", $params);
        $reports = Kemenag::where('bulan', $periods[1])->where('tahun', $periods[0])->get()->toArray();
        $kelurahan = Kelurahan::all();
        $index_report_non_kelurahan = array_keys(array_column($reports, 'kelurahan'), null)[0];
        $index_report_kelurahan = array_keys(array_column($reports, 'kelurahan'), !null);
        $index_report_kelurahan = [
            'start' => $index_report_kelurahan[0],
            'end' => $index_report_kelurahan[count($index_report_kelurahan) - 1]
        ];
        $report_non_kelurahan = array_slice($reports, $index_report_non_kelurahan, 1)[0];
        $report_kelurahan = array_slice($reports, $index_report_kelurahan['start'], $index_report_kelurahan['end']);
        $column_kelurahan_only = array_column($report_kelurahan, 'kelurahan');

        return view('pages.kemenag-edit', compact('kelurahan', 'report_non_kelurahan', 'report_kelurahan', 'column_kelurahan_only'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kemenag  $kemenag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kemenag $kemenag)
    {
        /**
         * Validasi kolom yang akan diproses
         */
        $validation = $request->validate([
            'date' => 'required',
            'terlaksana_kampanye_pencegahan_stunting' => 'required',
            'keterangan_terlaksana_kampanye_pencegahan_stunting' => 'nullable|string',
            'kelurahan' => 'required',
            'kelurahan.*' => 'sometimes|numeric',
            'calon_pasangan_usia_subur_memperoleh_pendampingan' => 'required',
            'calon_pasangan_usia_subur_memperoleh_pendampingan.*' => 'sometimes|numeric',
            'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan' => 'required',
            'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan.*' => 'sometimes|numeric',
            'persentase_layanan_bimbingan_perkawinan_materi_stunting' => 'required',
            'persentase_layanan_bimbingan_perkawinan_materi_stunting.*' => 'sometimes|numeric',
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
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Kemenag yaitu data pada sheet "Agama"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'terlaksana_kampanye_pencegahan_stunting' => $request->terlaksana_kampanye_pencegahan_stunting,
            'keterangan_terlaksana_kampanye_pencegahan_stunting' => $request->keterangan_terlaksana_kampanye_pencegahan_stunting
        ];

        /**
         * Siapkan data yang berbentuk per kelurahan, contohnya pada OPD Kemenag yaitu data pada sheet "Kesehatan (Data Supply)"
         * Pada proses ini, tambahkan kolom id seperti yg dapat dilihat pada contoh di bawah
         * Value untuk kolom id didapat dari hidden input pada form edit, silakan cek form edit dan cari textfield "id_report_kelurahan[]"
         */
        $per_kelurahan_data = [];
        for ($i = 0; $i < count($request->calon_pasangan_usia_subur_memperoleh_pendampingan); $i++) {
            array_push($per_kelurahan_data, [
                'id' => $request->id_report_kelurahan[$i],
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kelurahan' => $request->kelurahan[$i],
                'terlaksana_kampanye_pencegahan_stunting' => null,
                'keterangan_terlaksana_kampanye_pencegahan_stunting' => null,
                'calon_pasangan_usia_subur_memperoleh_pendampingan' => $request->calon_pasangan_usia_subur_memperoleh_pendampingan[$i],
                'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan' => $request->jumlah_pasangan_usia_subur_mendaftarkan_pernikahan[$i],
                'persentase_layanan_bimbingan_perkawinan_materi_stunting' => $request->persentase_layanan_bimbingan_perkawinan_materi_stunting[$i],
            ]);
        }

        /**
         * Update data yang sudah disiapkan
         * Untuk data non kelurahan (kolom isian ya/tidak) menggunakan perintah where lalu update
         * Parameter id_report_non_kelurahan dapat dilihat pada form edit, silakan cek form edit dan cari textfield "id_report_non_kelurahan"
         * Untuk data per kelurahan menggunakan perintah upsert untuk batch update, dengan 3 parameter
         * Parameter pertama yaitu data yg sudah disiapkan dan akan diupdate
         * Parameter kedua yaitu kolom yg harus bersifat unique (biarkan kosong)
         * Parameter ketiga yaitu kolom yang perlu diupdate apabila ada data yg sama
         * (silakan isikan sesuai dengan kolom pada OPD yang kalian kerjakan, kolom yg sama untuk semua OPD hanya tahun dan bulan)
         */
        $non_kelurahan_insert = Kemenag::where('id', $request->id_report_non_kelurahan)->update($non_kelurahan_data);
        $per_kelurahan_insert = Kemenag::upsert(
            $per_kelurahan_data,
            [],
            [
                'tahun',
                'bulan',
                'terlaksana_kampanye_pencegahan_stunting',
                'keterangan_terlaksana_kampanye_pencegahan_stunting',
                'terlaksana_kampanye_pencegahan_stunting',
                'keterangan_terlaksana_kampanye_pencegahan_stunting',
                'calon_pasangan_usia_subur_memperoleh_pendampingan',
                'jumlah_pasangan_usia_subur_mendaftarkan_pernikahan',
                'persentase_layanan_bimbingan_perkawinan_materi_stunting',
            ]
        );

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data && $per_kelurahan_data) return redirect('/form/kemenag')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }
}
