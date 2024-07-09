@extends('layouts.app')

@section('title', 'Sub Kegiatan')

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
            <h1>Sub Kegiatan</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('master.sub_kegiatan.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fas fa-back"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Kegiatan</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Kegiatan
                                            @error('fk_kegiatan_id')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        @if(auth()->user()->hasRole('OPD'))
                                            <input type="hidden" class="form-control" placeholder="Kode Sub Kegiatan" name="fk_kegiatan_id" value="{{ $data->sub_kegiatan->kegiatan->id ?? old('kode_sub_kegiatan') }}">
                                            <input required @if(auth()->user()->hasRole('OPD')) readonly @endif type="text" class="form-control" placeholder="Kode Sub Kegiatan" value="{{ $data->sub_kegiatan->kegiatan->nama_kegiatan ?? old('kode_sub_kegiatan') }}">
                                        @else
                                            <select class="form-control select2" name="fk_kegiatan_id" required @if(auth()->user()->hasRole('OPD')) readonly @endif>
                                                <option value="">Pilih Kegiatan</option>
                                                @foreach ($data->kegiatan as $item)
                                                    <option {{ isset($data->sub_kegiatan) && $data->sub_kegiatan->fk_kegiatan_id==$item->id ? 'selected':''  }} value="{{ $item->id }}">{{ $item->kode_kegiatan }} {{ $item->nama_kegiatan }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Kode Sub Kegiatan
                                            @error('kode_sub_kegiatan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required @if(auth()->user()->hasRole('OPD')) readonly @endif type="text" class="form-control" placeholder="Kode Sub Kegiatan" name="kode_sub_kegiatan" value="{{ $data->sub_kegiatan->kode_sub_kegiatan ?? old('kode_sub_kegiatan') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Nama Sub Kegiatan
                                            @error('nama_sub_kegiatan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required @if(auth()->user()->hasRole('OPD')) readonly @endif type="text" class="form-control" placeholder="Nama Sub Kegiatan" name="nama_sub_kegiatan" value="{{ $data->sub_kegiatan->nama_sub_kegiatan ??old('nama_sub_kegiatan') }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Anggaran Murni
                                            @error('anggaran_murni')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Anggaran Murni" name="anggaran_murni" value="{{ $data->sub_kegiatan->anggaran_murni ??old('anggaran_murni') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Perubahan Perbup 1
                                            @error('perubahan_perbup1')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Perubahan Perbup 1" name="perubahan_perbup1" value="{{ $data->sub_kegiatan->perubahan_perbup1 ??old('perubahan_perbup1') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Perubahan Perbup 2
                                            @error('perubahan_perbup2')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Perubahan Perbup 2" name="perubahan_perbup2" value="{{ $data->sub_kegiatan->perubahan_perbup2 ??old('perubahan_perbup2') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Perubahan Perbup 3
                                            @error('perubahan_perbup3')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Perubahan Perbup 3" name="perubahan_perbup3" value="{{ $data->sub_kegiatan->perubahan_perbup3 ??old('perubahan_perbup3') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Perubahan Perbup 4
                                            @error('perubahan_perbup4')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Perubahan Perbup 4" name="perubahan_perbup4" value="{{ $data->sub_kegiatan->perubahan_perbup4 ??old('perubahan_perbup4') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">
                                            Perubahan Anggaran
                                            @error('perubahan_anggaran')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control dec" placeholder="Perubahan Anggaran" name="perubahan_anggaran" value="{{ $data->sub_kegiatan->perubahan_anggaran ??old('perubahan_anggaran') }}">
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