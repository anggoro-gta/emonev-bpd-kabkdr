@extends('layouts.app')

@section('title', 'Realisasi Faktor TL')

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
            <h1>Realisasi Faktor TL</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('realisasi.faktortl.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fa-solid fa-chevron-left"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Realisasi Faktor TL</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">


                                <div class="form-row">
                                    @if(auth()->user()->hasRole('Admin'))
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            SKPD
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
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Faktor Pendorong Keberhasilan Kinerja
                                        </label>
                                            <textarea required style="height: 150px;" name="faktor_pendorong" class="form-control" data-height="150" rows="5">{{ $data->realisasi->faktor_pendorong ?? old('faktor_pendorong') }}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Faktor Penghambat pencapaian Kinerja
                                        </label>
                                            <textarea required style="height: 150px;" name="faktor_penghambat" class="form-control" data-height="150" rows="5">{{ $data->realisasi->faktor_penghambat ?? old('faktor_penghambat') }}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Tindak Lanjut yang diperlukan dalam triwulan berikutnya
                                        </label>
                                            <textarea required style="height: 150px;" name="tindaklanjut_tw_berikutnya" class="form-control" data-height="150" rows="5">{{ $data->realisasi->tindaklanjut_tw_berikutnya ?? old('tindaklanjut_tw_berikutnya') }}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Tindak Lanjut yang diperlukan dalam RKPD berikutnya
                                        </label>
                                            <textarea required style="height: 150px;" name="tindaklanjut_rkpd_berikutnya" class="form-control" data-height="150" rows="5">{{ $data->realisasi->tindaklanjut_rkpd_berikutnya ?? old('tindaklanjut_rkpd_berikutnya') }}</textarea>
                                    </div>
                                    <button class="btn btn-primary">Submit </button>
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
{{-- @include('realisasi::program.script') --}}
@endpush