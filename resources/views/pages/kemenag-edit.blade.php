@extends('layout.base')

@section('title', 'Edit Form Kemenag')

@section('main-content')

    <div class="row">
        <div class="col-12 d-flex flex-row align-items-center justify-content-between mb-4">
            <!-- Page Heading -->
            <h1 class="h3 text-gray-800">Form Edit Kemenag</h1>
        </div>
        @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if (session('error') || session('success'))
            <div class="col-12">
                <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissable fade show" role="alert">
                    {{ session('error') ?? session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('/form/kemenag/update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_report_non_kelurahan" value="{{ $report_non_kelurahan['id'] }}" readonly>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Bulan dan Tahun</label>
                            <div class="col-sm-2">
                                <input type="month" class="form-control" name="date" value="{{ $report_non_kelurahan['tahun'] .'-'. substr('0' . $report_non_kelurahan['bulan'], -2) }}">
                            </div>
                        </div>

                        <!-- Nav pills -->
                        <ul class="nav nav-tabs mt-5">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#agama">Agama</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#pus">PUS 4</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="agama">
                                <div class="table-responsive" style="max-height: 60vh; overflow: scroll">
                                    <div class="card" style="border-top: none; border-top-left-radius: 0;">
                                        <div class="card-body d-flex flex-column individual-question">
                                            <h5><strong>Terlaksananya forum komunikasi perubahan perilaku dalam penurunan stunting lintas agama</strong></h5>
                                            <span><strong>Target:</strong> 2 Kali</span>
                                            <span><strong>Tahun:</strong> Tiap Tahun</span>
                                            <span><strong>Urusan:</strong> Agama</span>
                                            <span>
                                                <strong>Status: </strong>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" value="ya" name="terlaksana_kampanye_pencegahan_stunting" @if($report_non_kelurahan['terlaksana_kampanye_pencegahan_stunting'] == 'ya') checked @endif>
                                                    <label class="form-check-label">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" value="tidak" name="terlaksana_kampanye_pencegahan_stunting" @if($report_non_kelurahan['terlaksana_kampanye_pencegahan_stunting'] == 'tidak') checked @endif>
                                                    <label class="form-check-label">Tidak</label>
                                                </div>
                                            </span>
                                            <span>
                                                <strong>Keterangan:</strong>
                                                <textarea name="keterangan_terlaksana_kampanye_pencegahan_stunting" class="form-control">{{ $report_non_kelurahan['keterangan_terlaksana_kampanye_pencegahan_stunting'] }}</textarea>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pus">
                                <div class="alert alert-info alert-dismissable fade show mt-3" role="alert">
                                    Setiap kolom harus diisi. Bila ada data yang kosong, masukkan "0".
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="table-responsive" style="max-height: 60vh; overflow: scroll">
                                    <table class="table table-striped table-bordered table-hover table-form">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Kecamatan</th>
                                                <th>Puskesmas</th>
                                                <th>Kelurahan</th>
                                                <th>Calon pasangan usian subur memperoleh pendampingan 3 bulan pra nikah</th>
                                                <th>jumlah pasangan usia subur yang mendaftarkan pernikahan  </th>
                                                <th>Persentase layanan pemeriksaan bimbingan perkawinan dengan materi stunting terhadap seluruh calon pengantin dalam kurun waktu yang sama</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 0;
                                            @endphp
                                            @foreach($kelurahan as $kel)
                                                <tr>
                                                    <td class="text-center">{{ ($i++) + 1 }}</td>
                                                    <td>{{ $kel->parent_kecamatan->kecamatan }}</td>
                                                    <td>{{ $kel->parent_puskesmas->puskesmas}}</td>
                                                    <td>
                                                        {{ $kel->kelurahan }}
                                                        <input type="hidden" name="kelurahan[]" value="{{ $kel->id }}" readonly>
                                                        <input type="hidden" name="id_report_kelurahan[]" value="{{ $report_kelurahan[array_search($kel->id, $column_kelurahan_only)]['id'] }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="calon_pasangan_usia_subur_memperoleh_pendampingan[]" class="form-control" value="{{ $report_kelurahan[array_search($kel->id, $column_kelurahan_only)]['calon_pasangan_usia_subur_memperoleh_pendampingan'] }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_pasangan_usia_subur_mendaftarkan_pernikahan[]" class="form-control" value="{{ $report_kelurahan[array_search($kel->id, $column_kelurahan_only)]['jumlah_pasangan_usia_subur_mendaftarkan_pernikahan'] }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="persentase_layanan_bimbingan_perkawinan_materi_stunting[]" class="form-control" value="{{ $report_kelurahan[array_search($kel->id, $column_kelurahan_only)]['persentase_layanan_bimbingan_perkawinan_materi_stunting'] }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button class="btn btn-outline-success mt-3 float-right">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection