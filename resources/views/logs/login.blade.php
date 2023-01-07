@extends('adminlte::page')

@section('title', 'Login logs')

@section('content_header')
    <h1>Login logs</h1>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Name',
    'Email',
    'Source',
    'Date',
];

$config = [
    'order' => [[0, 'desc']],
    'columns' => [
        [
            'data' => 'id',
            'visible' => false
        ],
        [
            'data' => 'name'
        ],
        [
            'data' => 'email'
        ],
        [
            'data' => 'source'
        ],
        [
            'data' => 'date',
            'type' => 'date'
        ]
    ],
    'paging' => true,
    'lengthMenu' => [ 10 ]
];

foreach ($entities as $i => &$o) {
    $temp = [
        'id' => $o['id'],
        'name' => $o['user']->name,
        'email' => $o['user']->email,
        'source' => $o['source'],
        'date' => $o['updated_at']->format("d-M-y h:i A")
    ];
    $entities[$i] = $temp;
}
$config['data'] = $entities;
@endphp



@section('content')
    <x-adminlte-datatable id="tblMain" :heads="$heads" :config="$config" theme="light" striped hoverable with-buttons>
    </x-adminlte-datatable>
@stop

@section('css')
    <link href="{{ asset('css/places-list.css') }}" rel="stylesheet">
@stop

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#tblMain').DataTable();
    });
</script>
@stop