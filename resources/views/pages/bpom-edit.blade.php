@extends('layout.base')

@section('title', 'Edit Form bpom')

@section('main-content')

    <div class="row">
        <div class="col-12 d-flex flex-row align-items-center justify-content-between mb-4">
            <!-- Page Heading -->
            <h1 class="h3 text-gray-800">Form Edit bpom</h1>
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
                    <form action="{{ url('/form/bpom/update') }}" method="POST">
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
                            <a class="nav-link active" data-toggle="pill" href="#bpom">bpom</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="bpom">
                            <div class="table-responsive" style="max-height: 60vh; overflow: scroll">
                                <div class="card" style="border-top: none; border-top-left-radius: 0;">
                                    <div class="card-body d-flex flex-column individual-question">
                                        <h5><strong>1. Terselenggaranya rembuk stunting tingkat kabupaten/kota</strong></h5>
                                        <span><strong>Target:</strong> Min 1 kali</span>
                                        <span><strong>Tahun:</strong> Tiap Tahun</span>
                                        <span>
                                            <strong>Status: </strong>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" value="ya" name="pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha" @if($report_non_kelurahan['pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha'] == 'ya') checked @endif>
                                                <label class="form-check-label">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" value="tidak" name="pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha" @if($report_non_kelurahan['pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha'] == 'tidak') checked @endif>
                                                <label class="form-check-label">Tidak</label>
                                            </div>
                                        </span>
                                        <span>
                                            <strong>Keterangan:</strong>
                                            <textarea name="keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha" class="form-control">{{ $report_non_kelurahan['keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha'] }}</textarea>
                                        </span>
                                    </div>
                                </div>
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