@extends('layouts.app')

@section('title', 'Permission')

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
            <h1>Permission</h1>

            <div class="section-header-breadcrumb">
                <a href="{{ route('setting.permission.index') }}" class="btn btn-primary" title="Kembali"><i
                        class="fas fa-back"></i></a>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Permission</h4>
                        </div>

                        <form method="POST" action="{{ $data->action }}">
                            @csrf
                            @method($data->method)
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">
                                            Permission

                                            @error('name')
                                            <code>{{ $message }}</code>
                                            @enderror
                                        </label>
                                        <input type="text" class="form-control" placeholder="Permission" name="name"
                                            value="{{ $data->permission->name ?? old('name') }}">
                                    </div>
                                </div>

                                @if(!$data->roles->isEmpty())

                                <h4>Assign Permission to Roles</h4>

                                <table class="table table-borderless">
                                    @foreach ($data->groupedRoles as $group)
                                    <tr>
                                        @foreach ($group as $role)
                                        <td>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" name="roles[]" id="" class="custom-control-input" value="{{ $role->id }}" {{ isset($data->permission) && in_array($role->id,$data->permission->roles->pluck('id')->toArray()) ? 'checked':'' }}>
                                                <span class="custom-control-label">{{ $role->name }}</span>
                                            </label>
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </table>
                                @endif
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