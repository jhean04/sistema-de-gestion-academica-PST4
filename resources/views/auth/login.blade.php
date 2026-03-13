@extends('adminlte::auth.login')

@section('auth_header', 'Ingresar al Sistema de Gestión Académica')

@section('auth_body')
<form action="{{ route('login') }}" method="post">
    @csrf

    {{-- Email field --}}
    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="{{ __('adminlte::adminlte.password') }}">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    {{-- CAPTCHA FIELD --}}
    <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
            <span class="captcha-image">
                <img src="{{ url('captcha/flat') }}?{{ rand() }}" alt="captcha">
            </span>F
            <button type="button" class="btn btn-outline-secondary btn-sm ml-2" id="refresh-captcha">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <div class="input-group">
            <input id="captcha" type="text"
                class="form-control @error('captcha') is-invalid @enderror"
                name="captcha"
                placeholder="Código de seguridad" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-shield-alt"></span>
                </div>
            </div>
            @error('captcha')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    {{-- Login button --}}
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                <span class="fas fa-sign-in-alt"></span>
                {{ __('adminlte::adminlte.sign_in') }}
            </button>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#refresh-captcha').click(function() {
            // Recarga la imagen directamente al src del elemento img
            $('.captcha-image img').attr('src', '{{ url("captcha/flat") }}?' + Math.random());
        });
    });
</script>
@stop