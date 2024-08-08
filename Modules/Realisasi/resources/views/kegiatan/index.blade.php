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
                            <form>
                                <div class="card-body">
                                    <div class="form-row">
                                        @if (auth()->user()->hasRole("Admin"))

                                            <div class="form-group col-md-3 mb-0">
                                                <label for="inputPassword4">
                                                    SKPD
                                                </label>
                                                <select class="form-control select2" name="fk_skpd_id">
                                                    <option value="">Pilih SKPD</option>
                                                    @foreach ($data->skpd_unit as $item)
                                                    <option {{ request('fk_skpd_id')==$item->id ? 'selected':'' }} value="{{
                                                        $item->id }}">{{ $item->nama_unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group col-md-3 mb-0">
                                            <label for="inputPassword4">
                                                Cari
                                            </label>
                                            <input type="text" name="q" value="{{ request()->q }}"
                                                class="form-control mb-2 mr-sm-2" placeholder="Cari">
                                        </div>
                                        <div class="form-group col-md-3 mb-0">
                                            <button class="btn btn-primary mt-4">Submit</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table-striped table" id="table-userx">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>OPD</th>
                                        <th>Triwulan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->realisasi as $key => $item)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $item->skpd->nama_skpd }}</td>
                                        <td>{{ $item->triwulan }}</td>
                                        <td>
                                            @if ($item->status_posting==0)
                                                <a href="{{ route('realisasi.kegiatan.edit',$item->id) }}"
                                                    class="btn btn-primary" title="Edit"><i
                                                        class="fas fa-pencil"></i></a>

                                                <a data-url="{{ route('realisasi.kegiatan.tooglePosting',$item->id) }}" data-text="Anda Yakin Posting" data-status_posting="1"
                                                    title="Posting" class="btn btn-success togglePosting">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @else

                                            <a href="{{ route('realisasi.kegiatan.show',$item->id) }}"
                                                class="btn btn-primary" title="Edit"><i
                                                    class="fas fa-eye"></i></a>
                                                @if(auth()->user()->hasRole('Admin'))
                                                    <a data-url="{{ route('realisasi.kegiatan.tooglePosting',$item->id) }}" data-text="Anda Yakin Batal Posting" data-status_posting="0"
                                                        title="Batal Posting" class="btn btn-danger togglePosting">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                @endif
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            {{-- {{ $data->skpd_unit->appends(['q'
                            =>request()->q])->links('vendor.pagination.bootstrap-5') }} --}}
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