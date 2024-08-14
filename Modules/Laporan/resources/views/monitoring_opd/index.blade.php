@extends('layouts.app')

@section('title', 'Bidang Urusan')

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
            <h1>Realisasi Program, Kegiatan, Sub Kegiatan</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <form>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3 mb-0">
                                        <label for="inputPassword4">
                                            Triwulan
                                        </label>
                                        <select class="form-control select2"  name="triwulan" id="triwulan" required>
                                            <option value="">Pilih Triwulan</option>
                                            <option {{ request()->triwulan=='1' ? 'selected':''  }} value="1">1</option>
                                            <option {{ request()->triwulan=='2' ? 'selected':''  }} value="2">2</option>
                                            <option {{ request()->triwulan=='3' ? 'selected':''  }} value="3">3</option>
                                            <option {{ request()->triwulan=='4' ? 'selected':''  }} value="4">4</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 mb-0">
                                        <label for="inputPassword4">
                                            Realisasi
                                        </label>
                                        <select class="form-control Realisasi"  name="realisasi" id="realisasi" required>
                                            <option value="">Pilih Realisasi</option>
                                            <option {{ request()->realisasi=='Program' ? 'selected':''  }} value="Program">Program</option>
                                            <option {{ request()->realisasi=='Kegiatan' ? 'selected':''  }} value="Kegiatan">Kegiatan</option>
                                            <option {{ request()->realisasi=='Sub Kegiatan' ? 'selected':''  }} value="Sub Kegiatan">Sub Kegiatan</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 mb-0">
                                        <button name="type" class="btn btn-success mt-4" value="tampil" type="submit">Tampil</button>
                                        <button name="type" class="btn btn-danger mt-4" value="cetak" type="submit">Cetak</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @if (isset($data->realisasi))

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table-striped table" id="table-userx">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama OPD</th>
                                            <th>Status Input</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $bgColor = [
                                                null => '#fc544b',
                                                0 => 'orange',
                                                1 => '#47c363'
                                            ];
                                            $label = [
                                                null => 'Belum Input',
                                                0 => 'Belum Posting',
                                                1 => 'Sudah Posting'
                                            ];
                                        @endphp
                                        @foreach ($data->realisasi as $key => $item)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $item->nama_skpd }}</td>
                                            <td style="color:white;background-color: {!! $bgColor[$item->status_posting] !!}">
                                                {{ $label[$item->status_posting] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="card-footer text-right">
                                {{ $data->bidang_urusan->appends(['q' =>request()->q])->links('vendor.pagination.bootstrap-5') }}
                            </div> --}}
                        </div>
                        @endif
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
@include('layouts._delete')
@endpush