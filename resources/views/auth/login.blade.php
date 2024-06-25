@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
@endpush

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Login</h4>
        </div>

        <div class="card-body">
            <form method="POST"
                action="{{ route('login') }}"
                class="needs-validation"
                novalidate="">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email"
                        type="text"
                        class="form-control"
                        name="username"
                        tabindex="1"
                        required
                        value="{{ old('email') }}"
                        autofocus>
                    <div class="invalid-feedback">
                        Please fill in your username
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-block">
                        <label for="password"
                            class="control-label">Password</label>
                        {{-- <div class="float-right">
                            <a href="auth-forgot-password.html"
                                class="text-small">
                                Forgot Password?
                            </a>
                        </div> --}}
                    </div>
                    <input id="password"
                        type="password"
                        class="form-control"
                        name="password"
                        tabindex="2"
                        required>
                    <div class="invalid-feedback">
                        please fill in your password
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun">Tahun</label>
                    <select name="tahunSession" class="form-control">
                        @for ($i = 2020; $i < 2031; $i++)
                            <option {{ date('Y')==$i ? 'selected':'' }} value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <div class="invalid-feedback">
                        Please fill in your username
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit"
                        class="btn btn-primary btn-lg btn-block"
                        tabindex="4">
                        Login
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
