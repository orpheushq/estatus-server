@extends('adminlte::page')

@section('title', 'Logbook')

@section('content_header')
@stop

@section('content_top_nav_left')
    <li class="nav-item">
        <a class="nav-link">{{ $patient->name }}</a>
    </li>
@stop

@section('content')

    <x-navtab :tabs="[
        ['link' => 'info', 'text' => 'Information'],
        ['link' => 'logbook', 'text' => 'Logbook'],
        ['link' => 'health', 'text' => 'Health data'],
        ['link' => 'remarks', 'text' => 'Remarks'],
        ['link' => 'about', 'text' => 'About patient']
    ]"/>
    
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop