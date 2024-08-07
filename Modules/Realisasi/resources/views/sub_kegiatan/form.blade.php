@extends('layouts.app')

@section('title', 'Realisasi Sub Kegiatan')

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
            <h1>Realisasi Sub Kegiatan</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('master.skpd_unit.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fa-solid fa-chevron-left"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Realisasi Sub Kegiatan</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                @if ($data->method!='POST')
                                    <table class="table">
                                        <tr>
                                            <th width="10%">SKPD</th>
                                            <th width="2%">:</th>
                                            <th>{{ $data->realisasi->skpd->nama_skpd  }}</th>
                                        </tr>
                                        <tr>
                                            <th>Triwulan</th>
                                            <th>:</th>
                                            <th>{{ $data->realisasi->triwulan  }}</th>
                                        </tr>
                                    </table>
                                @else

                                <div class="form-row">
                                    @if(auth()->user()->hasRole('Admin'))
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            SKPDaa
                                            @error('fk_skpd_id')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <select class="form-control select2 getData"  name="fk_skpd_id" id="fk_skpd_id" required>
                                            <option value="">Pilih SKPD</option>
                                            @foreach ($data->skpd as $item)
                                                <option {{ isset($data->realisasi) && $data->realisasi->fk_skpd_id==$item->id ? 'selected':''  }} value="{{ $item->id }}">{{ $item->nama_skpd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @else
                                    <input type="hidden" name="fk_skpd_id" id="fk_skpd_id" value="{{ auth()->user()->unit->skpd->id ?? null }}">
                                    @endif
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Triwulan
                                        </label>
                                        <select class="form-control select2 getData"  name="triwulan" id="triwulan" required>
                                            <option value="">Pilih Triwulan</option>
                                            <option {{ isset($data->realisasi) && $data->realisasi->triwulan=='1' ? 'selected':''  }} value="1">1</option>
                                            <option {{ isset($data->realisasi) && $data->realisasi->triwulan=='2' ? 'selected':''  }} value="2">2</option>
                                            <option {{ isset($data->realisasi) && $data->realisasi->triwulan=='3' ? 'selected':''  }} value="3">3</option>
                                            <option {{ isset($data->realisasi) && $data->realisasi->triwulan=='4' ? 'selected':''  }} value="4">4</option>
                                        </select>
                                    </div>
                                </div>

                                @endif
                                <div class="form-row" id="wrapDetail">
                                    @if (isset($data->realisasi))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-md">
                                                <tr>
                                                    <th rowspan="2" class="text-center">No</th>
                                                    <th rowspan="2" class="text-center">Sub Kegiatan</th>
                                                    <th rowspan="2" class="text-center">Anggaran</th>
                                                    <th rowspan="2" class="text-center">Indikator</th>
                                                    <th rowspan="2" class="text-center">Volume</th>
                                                    <th rowspan="2" class="text-center">Satuan</th>
                                                    <th colspan="2" class="text-center">Realisasi</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Anggaran</th>
                                                    <th class="text-center">Volume</th>
                                                </tr>
                                                @foreach ($data->realisasi->detail as $item)
                                                <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->subKegiatan->nama_sub_kegiatan ?? null }}</td>
                                                    <td class="text-right">{{ number_format($item->anggaran_sub_kegiatan) }}</td>
                                                    <td>{{ $item->indikator_sub_kegiatan }}</td>
                                                    <td class="text-right">{{ number_format($item->volume_sub_kegiatan) }}</td>
                                                    <td>{{ $item->satuan_sub_kegiatan }}</td>
                                                    <td>

                                                        <input type="text" name="anggaran_realisasi[]" value="{{ $item->anggaran_realisasi  }}" class="form-control mb-2 mr-sm-2 dec" placeholder="Anggaran">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="volume_realisasi[]" value="{{ $item->volume_realisasi  }}" class="form-control mb-2 mr-sm-2 dec" placeholder="Volume">
                                                    </td>
                                                </tr>

                                                @endforeach
                                            </table>
                                            @if ($data->realisasi->status_posting==0)
                                                <button class="btn btn-primary">Submit </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
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
@include('realisasi::sub_kegiatan.script')
@endpush