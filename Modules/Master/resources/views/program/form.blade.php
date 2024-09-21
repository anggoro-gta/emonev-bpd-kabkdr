@extends('layouts.app')

@section('title', 'Program')

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
                        class="fa-solid fa-chevron-left"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Program</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">

                             @if (auth()->user()->hasRole("Admin"))
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
                                            <option {{ isset($data->program) &&
                                                $data->program->kode_sub_unit_skpd==$item->kode_unit ? 'selected':'' }}
                                                value="{{ $item->kode_unit }}">{{ $item->kode_unit }} {{
                                                $item->nama_unit }}</option>
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
                                            <option {{ isset($data->program) &&
                                                $data->program->fk_bidang_urusan_id==$item->id ? 'selected':'' }}
                                                value="{{ $item->id }}"> {{ $item->nama_bidang_urusan }}</option>
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
                                        <input required type="text" class="form-control" placeholder="Kode Program"
                                            name="kode_program"
                                            value="{{ $data->program->kode_program ?? old('kode_program') }}">
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">
                                            Nama Program
                                            @error('nama_program')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Nama Program"
                                            name="nama_program"
                                            value="{{ $data->program->nama_program ??old('nama_program') }}">
                                    </div>
                                </div>
                            @else
                            <input type="hidden" name="kode_sub_unit_skpd" value="{{ $data->program->kode_sub_unit_skpd }}">
                            <input type="hidden" name="fk_bidang_urusan_id" value="{{ $data->program->fk_bidang_urusan_id }}">
                            <input type="hidden" name="kode_program" value="{{ $data->program->kode_program }}">
                            <input type="hidden" name="nama_program" value="{{ $data->program->nama_program }}">
                            <table>
                                <tr>
                                    <th>Kode Program</th>
                                    <th>:</th>
                                    <th>{{ $data->program->kode_program  }}</th>
                                </tr>
                                <tr>
                                    <th>Nama Program</th>
                                    <th>:</th>
                                    <th>{{ $data->program->nama_program  }}</th>
                                </tr>
                            </table>
                            <br>
                            @endif
                                <div class="form-row">
                                    <table class="table" width="100%" id="indikator">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th>Indikator</th>
                                                <th width="15%">Volume RPJMD</th>
                                                <th width="15%">Satuan RPJMD</th>
                                                <th width="15%">Volume RKPD</th>
                                                <th width="15%">Satuan RKPD</th>
                                                <th class="text-center" width="10%">

                                                    <a style="cursor: pointer; " class="text-primary tambah" title="Tambah"><i class="fas fa-plus"></i></a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($data->program->indikator ))

                                                @foreach ($data->program->indikator as $item)
                                                <tr>
                                                    <input type="hidden" name="program_indikator_id[]" value="{{ $item->id }}">
                                                    <td valign="top" class="text-center">{{ $loop->iteration }}</td>
                                                    <td valign="top" class="text-center"><textarea name="indikator_prog[]"
                                                            class="form-control" data-height="150" rows="5"> {{ $item->indikator_prog }}</textarea></td>
                                                    <td valign="top" class="text-center"><input type="text"
                                                            name="volume_prog_rpjmd[]" class="form-control nominal" value="{{ $item->volume_prog_rpjmd }}"></td>
                                                    <td valign="top" class="text-center"><input type="text" name="satuan_prog_rpjmd[]" value="{{ $item->satuan_prog_rpjmd }}"  class="form-control"></td>
                                                    <td valign="top" class="text-center"><input type="text"
                                                            name="volume_prog[]" class="form-control nominal" value="{{ $item->volume_prog }}"></td>
                                                    <td valign="top" class="text-center"><input type="text" name="satuan_prog[]" value="{{ $item->satuan_prog }}"  class="form-control"></td>
                                                    <td class="text-center">
                                                        <a data-url="/master/program/{{ $item->id }}/indikator" data-url="" class="text-danger delete" title="Hapus"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>

                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
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
@include('layouts._delete')
@include('master::program.script')
@endpush