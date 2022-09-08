<?php

namespace App\Http\Controllers;

use App\Models\Dppkb;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DppkbController extends Controller
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
        $report_history = Dppkb::select(DB::raw('MAX(id), tahun, bulan'))->groupBy('bulan', 'tahun')->get();
        extract(get_object_vars($this));

        return view('pages.Dppkb', compact('kelurahan', 'report_history', 'months'));
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
            //kb1supply
            'tersedia_data_hasil_surveilans_KBS' => 'required',
            'keterangan_tersedia_data_hasil_surveilans_KBS' => 'nullable|string',
            'tersedia_data_KBS_melalui_SI' => 'required',
            'keterangan_tersedia_data_KBS_melalui_SI' => 'nullable|string',
            'persentase_terima_pendamping_penurunan_stunting' => 'required',
            'ket_persentase_terima_pendamping_penurunan_stunting' => 'nullable|string',

            'kelurahan' => 'required',
            'kelurahan.*' => 'sometimes|numeric',
            //datasasaran
            'jumlah_keluarga' => 'required',
            'jumlah_keluarga.*' => 'sometimes|numeric',
            'jumlah_keluarga_beresiko_stunting' => 'required',
            'jumlah_keluarga_beresiko_stunting.*' => 'sometimes|numeric',
            'persentase' => 'required',
            'persentase.*' => 'sometimes|numeric',
            //bumil3
            'jml_PUS_belum_terpenuhi_kebutuhan_KB' => 'required',
            'jml_PUS_belum_terpenuhi_kebutuhan_KB.*' => 'sometimes|numeric',
            'jml_PUS_keseluruhan' => 'required',
            'jml_PUS_keseluruhan.*' => 'sometimes|numeric',
            'persentase_PUS_tidak_terlayani' => 'required',
            'persentase_PUS_tidak_terlayani.*' => 'sometimes|numeric',
            //bumil4
            'jml_kehamilan_tidak_diinginkan_KTD' => 'required',
            'jml_kehamilan_tidak_diinginkan_KTD.*' => 'sometimes|numeric',
            'jml_ibu_hamil' => 'required',
            'jml_ibu_hamil.*' => 'sometimes|numeric',
            'persentase_KTD_kehamilan_belum_direncanakan' => 'required',
            'persentase_KTD_kehamilan_belum_direncanakan.*' => 'sometimes|numeric',
            //kelberesiko4
            'Jml_ibu_pasca_bersalin_mendapat_pelayanan' => 'required',
            'Jml_ibu_pasca_bersalin_mendapat_pelayanan.*' => 'sometimes|numeric',
            'jml_seluruh_ibu_pasca_persalinan' => 'required',
            'jml_seluruh_ibu_pasca_persalinan.*' => 'sometimes|numeric',
            'persentase_layanan_KB_kurun_waktu_sama' => 'required',
            'persentase_layanan_KB_kurun_waktu_sama.*' => 'sometimes|numeric',
            //kelberesiko5
            'jml_keluarga_berisiko_stunting_memperoleh_pendampingan' => 'required',
            'jml_keluarga_berisiko_stunting_memperoleh_pendampingan.*' => 'sometimes|numeric',
            'jml_seluruh_keluarga_berisiko' => 'required',
            'jml_seluruh_keluarga_berisiko.*' => 'sometimes|numeric',
            'persentase_layanan_pendampingan_keluagra_berisiko' => 'required',
            'persentase_layanan_pendampingan_keluagra_berisiko.*' => 'sometimes|numeric',
            //okpus3
            'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => 'required',
            'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi.*' => 'sometimes|numeric',
            'jml_PUS_mendaftarkan_pernikahan' => 'required',
            'jml_PUS_mendaftarkan_pernikahan.*' => 'sometimes|numeric',
            'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => 'required',
            'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi.*' => 'sometimes|numeric',

            //kb2supply
            'PIK_dan_BKR_edukasi' => 'required',
            'PIK_dan_BKR_edukasi.*' => 'sometimes|numeric',
            'kelurahan_melaksanakan_BKB' => 'required',
            'kelurahan_melaksanakan_BKB.*' => 'sometimes|numeric',


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

        $existing_periode = Dppkb::where('tahun', $tahun)->where('bulan', $bulan)->first();
        if ($existing_periode) {
            return back()->with('error', "Data pada periode yang sama ({$this->months[$bulan - 1]} {$tahun}) sudah ada, tidak dapat menyimpan data duplikat.")->withInput();
        }


        /**
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Diskominfo yaitu data pada sheet "Kominfo"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            //kb1supply
            'tersedia_data_hasil_surveilans_KBS' => $request->tersedia_data_hasil_surveilans_KBS,
            'keterangan_tersedia_data_hasil_surveilans_KBS' => $request->keterangan_tersedia_data_hasil_surveilans_KBS,
            'tersedia_data_KBS_melalui_SI' => $request->tersedia_data_KBS_melalui_SI,
            'keterangan_tersedia_data_KBS_melalui_SI' => $request->keterangan_tersedia_data_KBS_melalui_SI,
            'persentase_terima_pendamping_penurunan_stunting' => $request->persentase_terima_pendamping_penurunan_stunting,
            'ket_persentase_terima_pendamping_penurunan_stunting' => $request->ket_persentase_terima_pendamping_penurunan_stunting
        ];

        /**
         * Siapkan data yang berbentuk per kelurahan, contohnya pada OPD Diskominfo yaitu data pada sheet "Kesehatan (Data Supply)"
         */
        $per_kelurahan_data = [];
        for ($i = 0; $i < count($request->jumlah_keluarga); $i++) {
            array_push(
                $per_kelurahan_data,
                [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'kelurahan' => $request->kelurahan[$i],
                    //kb1supply
                    'tersedia_data_hasil_surveilans_KBS' => null,
                    'keterangan_tersedia_data_hasil_surveilans_KBS' => null,
                    'tersedia_data_KBS_melalui_SI' => null,
                    'keterangan_tersedia_data_KBS_melalui_SI' => null,
                    'persentase_terima_pendamping_penurunan_stunting' => null,
                    'ket_persentase_terima_pendamping_penurunan_stunting' => null,
                    //datasasaran
                    'jumlah_keluarga' => $request->jumlah_keluarga[$i],
                    'jumlah_keluarga_beresiko_stunting' => $request->jumlah_keluarga_beresiko_stunting[$i],
                    'persentase' => $request->persentase[$i],
                    ////bumil3
                    'jml_PUS_belum_terpenuhi_kebutuhan_KB' => $request->jml_PUS_belum_terpenuhi_kebutuhan_KB[$i],
                    'jml_PUS_keseluruhan'  => $request->jml_PUS_keseluruhan[$i],
                    'persentase_PUS_tidak_terlayani' => $request->persentase_PUS_tidak_terlayani[$i],
                    //bumil4
                    'jml_kehamilan_tidak_diinginkan_KTD' => $request->jml_kehamilan_tidak_diinginkan_KTD[$i],
                    'jml_ibu_hamil' => $request->jml_ibu_hamil[$i],
                    'persentase_KTD_kehamilan_belum_direncanakan' => $request->persentase_KTD_kehamilan_belum_direncanakan[$i],
                    //kelberesiko4
                    'Jml_ibu_pasca_bersalin_mendapat_pelayanan' => $request->Jml_ibu_pasca_bersalin_mendapat_pelayanan[$i],
                    'jml_seluruh_ibu_pasca_persalinan' => $request->jml_seluruh_ibu_pasca_persalinan[$i],
                    'persentase_layanan_KB_kurun_waktu_sama' => $request->persentase_layanan_KB_kurun_waktu_sama[$i],
                    //kelberesiko5
                    'jml_keluarga_berisiko_stunting_memperoleh_pendampingan' => $request->jml_keluarga_berisiko_stunting_memperoleh_pendampingan[$i],
                    'jml_seluruh_keluarga_berisiko' => $request->jml_seluruh_keluarga_berisiko[$i],
                    'persentase_layanan_pendampingan_keluagra_berisiko' => $request->persentase_layanan_pendampingan_keluagra_berisiko[$i],
                    //okpus3
                    'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => $request->calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi[$i],
                    'jml_PUS_mendaftarkan_pernikahan' => $request->jml_PUS_mendaftarkan_pernikahan[$i],
                    'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => $request->cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi[$i],

                    //kb2supply
                    'PIK_dan_BKR_edukasi' => $request->PIK_dan_BKR_edukasi[$i],
                    'kelurahan_melaksanakan_BKB' => $request->kelurahan_melaksanakan_BKB[$i],


                ]
            );
        }

        /**
         * Submit data yang sudah disiapkan
         * Untuk data per kelurahan menggunakan perintah upsert untuk batch insert
         */
        $non_kelurahan_insert = Dppkb::create($non_kelurahan_data);
        $per_kelurahan_insert = Dppkb::upsert($per_kelurahan_data, []);

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data && $per_kelurahan_data) return redirect('/form/Dppkb')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dppkb  $Dppkb
     * @return \Illuminate\Http\Response
     */
    public function edit(Dppkb $Dppkb, $params)
    {
        $periods = explode("-", $params);
        $reports = Dppkb::where('bulan', $periods[1])->where('tahun', $periods[0])->get()->toArray();
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

        return view('pages.Dppkb-edit', compact('kelurahan', 'report_non_kelurahan', 'report_kelurahan', 'column_kelurahan_only'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dppkb  $Dppkb
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dppkb $Dppkb)
    {
        /**
         * Validasi kolom yang akan diproses
         */
        $validation = $request->validate([
            'date' => 'required',
            //kb1supply
            'tersedia_data_hasil_surveilans_KBS' => 'required',
            'keterangan_tersedia_data_hasil_surveilans_KBS' => 'nullable|string',
            'tersedia_data_KBS_melalui_SI' => 'required',
            'keterangan_tersedia_data_KBS_melalui_SI' => 'nullable|string',
            'persentase_terima_pendamping_penurunan_stunting' => 'required',
            'ket_persentase_terima_pendamping_penurunan_stunting' => 'nullable|string',

            'kelurahan' => 'required',
            'kelurahan.*' => 'sometimes|numeric',
            //datasasaran
            'jumlah_keluarga' => 'required',
            'jumlah_keluarga.*' => 'sometimes|numeric',
            'jumlah_keluarga_beresiko_stunting' => 'required',
            'jumlah_keluarga_beresiko_stunting.*' => 'sometimes|numeric',
            'persentase' => 'required',
            'persentase.*' => 'sometimes|numeric',
            //bumil3
            'jml_PUS_belum_terpenuhi_kebutuhan_KB' => 'required',
            'jml_PUS_belum_terpenuhi_kebutuhan_KB.*' => 'sometimes|numeric',
            'jml_PUS_keseluruhan' => 'required',
            'jml_PUS_keseluruhan.*' => 'sometimes|numeric',
            'persentase_PUS_tidak_terlayani' => 'required',
            'persentase_PUS_tidak_terlayani.*' => 'sometimes|numeric',
            //bumil4
            'jml_kehamilan_tidak_diinginkan_KTD' => 'required',
            'jml_kehamilan_tidak_diinginkan_KTD.*' => 'sometimes|numeric',
            'jml_ibu_hamil' => 'required',
            'jml_ibu_hamil.*' => 'sometimes|numeric',
            'persentase_KTD_kehamilan_belum_direncanakan' => 'required',
            'persentase_KTD_kehamilan_belum_direncanakan.*' => 'sometimes|numeric',
            //kelberesiko4
            'Jml_ibu_pasca_bersalin_mendapat_pelayanan' => 'required',
            'Jml_ibu_pasca_bersalin_mendapat_pelayanan.*' => 'sometimes|numeric',
            'jml_seluruh_ibu_pasca_persalinan' => 'required',
            'jml_seluruh_ibu_pasca_persalinan.*' => 'sometimes|numeric',
            'persentase_layanan_KB_kurun_waktu_sama' => 'required',
            'persentase_layanan_KB_kurun_waktu_sama.*' => 'sometimes|numeric',
            //kelberesiko5
            'jml_keluarga_berisiko_stunting_memperoleh_pendampingan' => 'required',
            'jml_keluarga_berisiko_stunting_memperoleh_pendampingan.*' => 'sometimes|numeric',
            'jml_seluruh_keluarga_berisiko' => 'required',
            'jml_seluruh_keluarga_berisiko.*' => 'sometimes|numeric',
            'persentase_layanan_pendampingan_keluagra_berisiko' => 'required',
            'persentase_layanan_pendampingan_keluagra_berisiko.*' => 'sometimes|numeric',
            //okpus3
            'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => 'required',
            'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi.*' => 'sometimes|numeric',
            'jml_PUS_mendaftarkan_pernikahan' => 'required',
            'jml_PUS_mendaftarkan_pernikahan.*' => 'sometimes|numeric',
            'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => 'required',
            'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi.*' => 'sometimes|numeric',

            //kb2supply
            'PIK_dan_BKR_edukasi' => 'required',
            'PIK_dan_BKR_edukasi.*' => 'sometimes|numeric',
            'kelurahan_melaksanakan_BKB' => 'required',
            'kelurahan_melaksanakan_BKB.*' => 'sometimes|numeric',
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
         * Siapkan data yang berbentuk kolom isian status ya/tidak, contohnya pada OPD Diskominfo yaitu data pada sheet "supply 1"
         */
        $non_kelurahan_data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            //kb1supply
            'tersedia_data_hasil_surveilans_KBS' => $request->tersedia_data_hasil_surveilans_KBS,
            'keterangan_tersedia_data_hasil_surveilans_KBS' => $request->keterangan_tersedia_data_hasil_surveilans_KBS,
            'tersedia_data_KBS_melalui_SI' => $request->tersedia_data_KBS_melalui_SI,
            'keterangan_tersedia_data_KBS_melalui_SI' => $request->keterangan_tersedia_data_KBS_melalui_SI,
            'persentase_terima_pendamping_penurunan_stunting' => $request->persentase_terima_pendamping_penurunan_stunting,
            'ket_persentase_terima_pendamping_penurunan_stunting' => $request->ket_persentase_terima_pendamping_penurunan_stunting
        ];

        /**
         * Siapkan data yang berbentuk per kelurahan, contohnya pada OPD Diskominfo yaitu data pada sheet "Kesehatan (Data Supply)"
         * Pada proses ini, tambahkan kolom id seperti yg dapat dilihat pada contoh di bawah
         * Value untuk kolom id didapat dari hidden input pada form edit, silakan cek form edit dan cari textfield "id_report_kelurahan[]"
         */
        $per_kelurahan_data = [];
        for ($i = 0; $i < count($request->jumlah_keluarga); $i++) {
            array_push(
                $per_kelurahan_data,
                [
                    'id' => $request->id_report_kelurahan[$i],
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'kelurahan' => $request->kelurahan[$i],
                    //kb1supply
                    'tersedia_data_hasil_surveilans_KBS' => null,
                    'keterangan_tersedia_data_hasil_surveilans_KBS' => null,
                    'tersedia_data_KBS_melalui_SI' => null,
                    'keterangan_tersedia_data_KBS_melalui_SI' => null,
                    'persentase_terima_pendamping_penurunan_stunting' => null,
                    'ket_persentase_terima_pendamping_penurunan_stunting' => null,
                    //datasasaran
                    'jumlah_keluarga' => $request->jumlah_keluarga[$i],
                    'jumlah_keluarga_beresiko_stunting' => $request->jumlah_keluarga_beresiko_stunting[$i],
                    'persentase' => $request->persentase[$i],
                    //bumil3
                    'jml_PUS_belum_terpenuhi_kebutuhan_KB' => $request->jml_PUS_belum_terpenuhi_kebutuhan_KB[$i],
                    'jml_PUS_keseluruhan' => $request->jml_PUS_keseluruhan[$i],
                    'persentase_PUS_tidak_terlayani' => $request->persentase_PUS_tidak_terlayani[$i],
                    //bumil4
                    'jml_kehamilan_tidak_diinginkan_KTD' => $request->jml_kehamilan_tidak_diinginkan_KTD[$i],
                    'jml_ibu_hamil' => $request->jml_ibu_hamil[$i],
                    'persentase_KTD_kehamilan_belum_direncanakan' => $request->persentase_KTD_kehamilan_belum_direncanakan[$i],
                    //kelberesiko4
                    'Jml_ibu_pasca_bersalin_mendapat_pelayanan' => $request->Jml_ibu_pasca_bersalin_mendapat_pelayanan[$i],
                    'jml_seluruh_ibu_pasca_persalinan' => $request->jml_seluruh_ibu_pasca_persalinan[$i],
                    'persentase_layanan_KB_kurun_waktu_sama' => $request->persentase_layanan_KB_kurun_waktu_sama[$i],
                    //kelberesiko5
                    'jml_keluarga_berisiko_stunting_memperoleh_pendampingan' => $request->jml_keluarga_berisiko_stunting_memperoleh_pendampingan[$i],
                    'jml_seluruh_keluarga_berisiko' => $request->jml_seluruh_keluarga_berisiko[$i],
                    'persentase_layanan_pendampingan_keluagra_berisiko' => $request->persentase_layanan_pendampingan_keluagra_berisiko[$i],
                    //Okpus3
                    'calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => $request->calon_PUS_memperoleh_pendampingan_kesehatan_reproduksi[$i],
                    'jml_PUS_mendaftarkan_pernikahan' => $request->jml_PUS_mendaftarkan_pernikahan[$i],
                    'cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi' => $request->cln_PUS_memperoleh_pendampingan_kesehatan_reproduksi[$i],

                    //kb2supply
                    'PIK_dan_BKR_edukasi' => $request->PIK_dan_BKR_edukasi[$i],
                    'kelurahan_melaksanakan_BKB' => $request->kelurahan_melaksanakan_BKB[$i],
                ]
            );
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
        $non_kelurahan_insert = Dppkb::where('id', $request->id_report_non_kelurahan)->update($non_kelurahan_data);
        $per_kelurahan_insert = Dppkb::upsert(
            $per_kelurahan_data,
            [],
            [
                'tahun',
                'bulan',
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
            ]
        );

        /**
         * Kembali ke halaman sebelumnya dengan pesan berhasil atau gagal
         */
        if ($non_kelurahan_data && $per_kelurahan_data) return redirect('/form/Dppkb')->with('success', 'Data berhasil disimpan.');

        return back()->with('error', 'Gagal menyimpan data')->withInput();
    }
}
