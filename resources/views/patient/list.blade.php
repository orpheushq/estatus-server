@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
    <h1>Patients</h1>
@stop

@section('content')

    @foreach ($patients as $p)
        <div class="d-inline-flex m-2 card" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">{{ $p->name }}</h5>
                <p class="card-text">Info</p>
                <a href="{{ url('/patients/') }}/{{ $p->id }}" class="btn btn-primary">View</a>
            </div>
        </div>
    @endforeach

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop