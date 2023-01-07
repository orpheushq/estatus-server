@extends('adminlte::auth.login')

@section('auth_footer')
    @parent
    <p class="my-0 orpheus-brand">
        <a href="https://orpheus.digital/">Solved by Orpheus Digital</a>
    </p>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login_custom.css') }}">
@stop