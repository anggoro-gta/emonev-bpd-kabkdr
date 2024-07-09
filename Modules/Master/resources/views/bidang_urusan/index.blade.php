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
            <h1>Bidang Urusan</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('master.bidang_urusan.create') }}" class="btn btn-success" title="Tambah User"><i class="fas fa-plus"></i></a>
            </div>
        </div>

        <div class="section-body">
            @include('layouts._messages')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4></h4>
                            <div class="card-header-form">
                                <form>
                                    <div class="input-group">
                                        <input type="text" name="q" value="{{ request()->q }}" class="form-control"
                                            placeholder="Search">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table-striped table" id="table-userx">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Urusan</th>
                                            <th>Kode Bidang Urusan</th>
                                            <th>Nama Bidang Urusan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->bidang_urusan as $key => $item)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $item->urusan->nama_urusan }}</td>
                                            <td>{{ $item->kode_bidang_urusan }}</td>
                                            <td>{{ $item->nama_bidang_urusan }}</td>
                                            <td>
                                                @can('setting.user.update')
                                                <a href="{{ route('master.bidang_urusan.edit',$item->id) }}"
                                                    class="btn btn-primary" title="Edit Bidang Urusan"><i
                                                        class="fas fa-pencil"></i></a>
                                                @endcan
                                                @can('setting.user.destroy')
                                                    <a data-url="{{ route('master.bidang_urusan.destroy',$item->id) }}" title="Hapus"
                                                        class="btn btn-danger delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                @endcan

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                {{ $data->bidang_urusan->appends(['q' =>request()->q])->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
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