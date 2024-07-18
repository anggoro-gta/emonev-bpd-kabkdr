@extends('layouts.app')

@section('title', 'Urusan')

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
            <h1>Urusan</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('master.urusan.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fa-solid fa-chevron-left"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Urusan</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">
                                            Kode Urusan

                                            @error('kode_urusan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="number" class="form-control" placeholder="Kode Urusan" name="kode_urusan" value="{{ $data->urusan->kode_urusan ?? old('kode_urusan') }}">
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">
                                            Nama Urusan
                                            @error('nama_urusan')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input required type="text" class="form-control" placeholder="Nama Urusan" name="nama_urusan" value="{{ $data->urusan->nama_urusan ??old('nama_urusan') }}">
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