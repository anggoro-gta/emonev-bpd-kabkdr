@extends('layouts.app')

@section('title', 'User')

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
            <h1>User</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('setting.user.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fas fa-back"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form User</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Nama

                                            @error('name')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control" placeholder="Nama" name="name" value="{{ $data->user->name ?? old('name') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Email
                                            @error('email')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control" placeholder="Email" name="email" value="{{ $data->user->email ??old('email') }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Password

                                            @error('password')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="password" class="form-control" placeholder="Password" name="password">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Konfirmasi Password

                                            @error('password_confirmation')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="password" class="form-control" placeholder="Konfirmasi Password" name="password_confirmation">
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