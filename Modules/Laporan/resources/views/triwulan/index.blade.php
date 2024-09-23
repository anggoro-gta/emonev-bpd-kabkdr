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

    .card .card-header h4+.card-header-action,
    .card .card-header h4+.card-header-form {
        margin-left: 0 !important;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Realisasi Kegiatan</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('realisasi.kegiatan.create') }}" class="btn btn-success" title="Tambah"><i
                        class="fas fa-plus"></i></a>
            </div>
        </div>

        <div class="section-body">
            @include('layouts._messages')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('laporan.triwulan.cetak') }}" method="POST">
                                @csrf
                                @method('POST')
                                <div class="card-body">
                                    <div class="form-row">
                                        @if (auth()->user()->hasRole("Admin"))

                                            <div class="form-group col-md-3 mb-0">
                                                <label for="inputPassword4">
                                                    SKPD
                                                </label>
                                                <select class="form-control select2" name="kode_sub_unit_skpd" required>
                                                    <option value="">Pilih SKPD</option>
                                                    @foreach ($data->skpd_unit as $item)
                                                    <option {{ request('kode_sub_unit_skpd')==$item->id ? 'selected':'' }} value="{{
                                                        $item->kode_unit }}">{{ $item->nama_unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group col-md-3 mb-0">
                                            <label for="inputPassword4">
                                                Triwulan
                                            </label>
                                            <select class="form-control select2" name="triwulan" required>
                                                <option value="">Pilih Triwulan</option>
                                                <option  value="1">I</option>
                                                <option  value="2">II</option>
                                                <option  value="3">III</option>
                                                <option  value="4">IV</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3 mb-0">
                                            <button class="btn btn-danger mt-4" name="type" value="PDF">Pdf</button>
                                            <button class="btn btn-success mt-4" name="type" value="Excel">Excel</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
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
<script>
    $(document).on("click", ".togglePosting", function() {
        let url = $(this).data('url');
        let status_posting = $(this).data('status_posting');
        let text = $(this).data('text');
        $that = $(this);
        Swal.fire({
            title: 'Konfirmasi',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ya'
        }).then((result) => {
            if (result.isConfirmed) {
                let csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        '_token': csrf_token,status_posting
                    },
                    beforeSend: function() {
                        $('.swal2-confirm').prop('disabled', true);
                        $('.swal2-confirm').html('Loading...');
                        $that.closest('tr').css('background', '#f7f1f1')
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        swalError(data.msg);
                    }
                });
            }
        })
    })
</script>
@endpush