@extends('layouts.app')

@section('title', 'Kegiatan')

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
            <h1>Kegiatan</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('master.kegiatan.index') }}" class="btn btn-primary" title="Kembali"><i
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
                                            Program
                                            @error('fk_program_id')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <select class="form-control select2" name="fk_program_id" required>
                                            <option value="">Pilih Program</option>
                                            @foreach ($data->program as $item)
                                                <option {{ isset($data->kegiatan) && $data->kegiatan->fk_program_id==$item->id ? 'selected':''  }} value="{{ $item->id }}">{{ $item->kode_program }} {{ $item->nama_program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Kode Kegiatan
                                            @error('kode_kegiatan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Kode Kegiatan" name="kode_kegiatan" value="{{ $data->kegiatan->kode_kegiatan ?? old('kode_kegiatan') }}">
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">
                                            Nama Kegiatan
                                            @error('nama_kegiatan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Nama Kegiatan" name="nama_kegiatan" value="{{ $data->kegiatan->nama_kegiatan ??old('nama_kegiatan') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <table class="table" width="100%" id="indikator">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Indikator</th>
                                            <th width="15%">Volume</th>
                                            <th width="15%">Satuan</th>
                                            <th class="text-center" width="10%">

                                                <a style="cursor: pointer; " class="text-primary tambah" title="Tambah"><i class="fas fa-plus"></i></a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($data->kegiatan->indikator ))

                                            @foreach ($data->kegiatan->indikator as $item)
                                            <tr>
                                                <input type="hidden" name="kegiatan_indikator_id[]" value="{{ $item->id }}">
                                                <td valign="top" class="text-center">{{ $loop->iteration }}</td>
                                                <td valign="top" class="text-center"><textarea name="indikator_keg[]"
                                                        class="form-control" data-height="150" rows="5"> {{ $item->indikator_keg }}</textarea></td>
                                                <td valign="top" class="text-center"><input type="text"
                                                        name="volume_keg[]" class="form-control dec" value="{{ $item->volume_keg }}"></td>
                                                <td valign="top" class="text-center"><input type="text" name="satuan_keg[]" value="{{ $item->satuan_keg }}"  class="form-control"></td>
                                                <td class="text-center">
                                                    <a data-url="/master/kegiatan/{{ $item->id }}/indikator" data-url="" class="text-danger delete" title="Hapus"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>

                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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
@include('master::kegiatan.script')
@include('layouts._delete')
@endpush