@extends('layouts.app')

@section('title', 'Realisasi Kegiatan')

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
            <h1>Realisasi Kegiatan</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('realisasi.program.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fa-solid fa-chevron-left"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Realisasi Kegiatan</h4>
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
                                            <th>{{ $data->header->skpd->nama_skpd  }}</th>
                                        </tr>
                                        <tr>
                                            <th>Triwulan</th>
                                            <th>:</th>
                                            <th>{{ $data->header->triwulan  }}</th>
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
                                                <option {{ isset($data->header) && $data->header->fk_skpd_id==$item->id ? 'selected':''  }} value="{{ $item->id }}">{{ $item->nama_skpd }}</option>
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
                                            <option {{ isset($data->header) && $data->header->triwulan=='1' ? 'selected':''  }} value="1">1</option>
                                            <option {{ isset($data->header) && $data->header->triwulan=='2' ? 'selected':''  }} value="2">2</option>
                                            <option {{ isset($data->header) && $data->header->triwulan=='3' ? 'selected':''  }} value="3">3</option>
                                            <option {{ isset($data->header) && $data->header->triwulan=='4' ? 'selected':''  }} value="4">4</option>
                                        </select>
                                    </div>
                                </div>

                                @endif
                                <div class="form-row" id="wrapDetail">
                                    @if (isset($data->header))
                                        <div class="table-responsive">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-md">
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Program</th>
                                                        <th class="text-center">Indikator</th>
                                                        <th class="text-center">Volume</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Realisasi Volume</th>
                                                    </tr>
                                                    @php
                                                        $indikatorKosong = 0;
                                                    @endphp
                                                    @foreach ($data->realisasi as $item)
                                                    <input type="hidden" name="id[]" value="{{ $item->id ?? null}}">
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->nama_program }}</td>
                                                        <input type="hidden" name="indikator_prog[]" value="{{ $item->indikator_prog }}">
                                                        <input type="hidden" name="satuan_prog[]" value="{{ $item->volume_prog }}">
                                                        <input type="hidden" name="volume_prog[]" value="{{ $item->satuan_prog }}">
                                                            <td>{{ $item->indikator_prog }}</td>
                                                            <td>{{ number_format($item->volume_prog) }}</td>
                                                            <td>{{ $item->satuan_prog }}</td>
                                                            <td>
                                                                <input type="text" name="volume_realisasi[]" value="{{ $item->volume_realisasi}}" class="form-control mb-2 mr-sm-2 dec" placeholder="Volume">
                                                            </td>
                                                    </tr>

                                                    @endforeach
                                                </table>
                                            </div>
                                            @if ($data->header->status_posting==0)
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
@include('realisasi::program.script')
@endpush