@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
    <h1>Patients</h1>
@stop

@section('content')

    <div class="d-flex flex-sm-row flex-column flex-wrap">
        @foreach ($patients as $p)
            <div class="d-flex col-sm-2 m-sm-2 card">
                <div class="card-body">
                    <h5 class="card-title">{{ $p->name }}</h5>
                    <p class="card-text">Info</p>
                    <a href="{{ url('/patients/') }}/{{ $p->id }}" class="btn btn-primary">View</a>
                </div>
            </div>
        @endforeach
    </div>
    
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop