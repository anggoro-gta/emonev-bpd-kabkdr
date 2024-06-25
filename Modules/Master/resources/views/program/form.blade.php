@extends('layouts.app')

@section('title', 'OPD')

@push('style')
<!-- CSS Libraries -->

<link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0px;
        margin-left: 0px;
        display: inline;
        border: 0px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border: 0px;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Program</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('master.program.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fas fa-back"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Program {{ session('tahunSession') }}</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            OPD Unit
                                            @error('kode_sub_unit_skpd')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <select class="form-control select2" name="kode_sub_unit_skpd" required>
                                            <option value="">Pilih OPD Unit</option>
                                            @foreach ($data->unit as $item)
                                                <option {{ isset($data->program) && $data->program->kode_sub_unit_skpd==$item->kode_unit ? 'selected':''  }} value="{{ $item->kode_unit }}">{{ $item->kode_unit }} {{ $item->nama_unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Bidang Urusan
                                            @error('fk_bidang_urusan_id')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <select class="form-control select2" name="fk_bidang_urusan_id" required>
                                            <option value="">Pilih Bidang</option>
                                            @foreach ($data->bidang as $item)
                                                <option {{ isset($data->program) && $data->program->fk_bidang_urusan_id==$item->id ? 'selected':''  }} value="{{ $item->id }}"> {{ $item->nama_bidang_urusan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Kode Program
                                            @error('kode_program')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Kode Program" name="kode_program" value="{{ $data->program->kode_program ?? old('kode_program') }}">
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">
                                            Nama Program
                                            @error('nama_program')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Nama Program" name="nama_program" value="{{ $data->program->nama_program ??old('nama_program') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<!-- JS Libraies -->
<script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="{{ asset('js/page/components-table.js') }}"></script>
<script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
@include('setting::user.script')
@endpush