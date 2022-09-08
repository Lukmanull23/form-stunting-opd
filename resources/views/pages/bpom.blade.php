@extends('layout.base')

@section('title', 'Form Bpom')

@section('main-content')

    <div class="row">
        <div class="col-12 d-flex flex-row align-items-center justify-content-between mb-4">
            <!-- Page Heading -->
            <h1 class="h3 text-gray-800">Form Input BPOM</h1>
            <button class="btn btn-outline-primary ml-4" data-toggle="modal" data-target="#form-history">Riwayat Input</button>
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
                    <form action="{{ url('/form/bpom/submit') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Bulan dan Tahun</label>
                            <div class="col-sm-2">
                                <input type="month" class="form-control" name="date" value="{{ old('date') }}">
                            </div>
                        </div>

                        <!-- Nav pills -->
                        <ul class="nav nav-tabs mt-5">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#bpom">BPOM</a>
                            </li>
                            
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="bpom">
                                <div class="table-responsive" style="max-height: 60vh; overflow: scroll">
                                    <div class="card" style="border-top: none; border-top-left-radius: 0;">
                                        <div class="card-body d-flex flex-column individual-question">
                                            <h5><strong> Pangan fortifikasi yang ditindaklanjuti oleh pelaku usaha</strong></h5>
                                            <span><strong>Target:</strong> 75%</span>
                                            <span><strong>Tahun:</strong> 2024</span>
                                            <span><strong>Urusan:</strong> Badan POM</span>
                                            <span>
                                                <strong>Status: </strong>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" value="ya" name="pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha">
                                                    <label class="form-check-label">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" value="tidak" name="pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha">
                                                    <label class="form-check-label">Tidak</label>
                                                </div>
                                            </span>
                                            <span>
                                                <strong>Keterangan:</strong>
                                                <textarea name="keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha" class="form-control">{{ old('keterangan_pangan_fortifikasi_ditindaklanjuti_oleh_pelaku_usaha') }}</textarea>
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

@section('modal-section')
    <div class="modal fade" id="form-history">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Input</h5>
                    <button class="close">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Bulan & Tahun</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $no = 1;
                        @endphp
                            @foreach ($report_history as $report)
                                <tr class="text-center">
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $months[$report->bulan - 1] . " " . $report->tahun }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-warning" href="{{ url('form/bpom/' . "$report->tahun-$report->bulan") }}">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection