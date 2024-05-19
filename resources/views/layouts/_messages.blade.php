@if(Session::has('flash_message'))
<div class="alert alert-success">
    <strong>Sukses!</strong> {!! session('flash_message') !!}
</div>
@endif


@if (Session::has('errors'))
<div class="alert alert-danger">
    <strong>Error!</strong> {!! session('errors') !!}
</div>
@endif