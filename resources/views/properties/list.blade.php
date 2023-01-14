@extends('adminlte::page')

@section('title', 'Properties')

@section('content_header')
    <h1>Properties</h1>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Type',
    'Size',
    'Area',
    'Title',
    'Description',
    'Created At',
    'Updated At',
    'Action'
    /*['label' => 'Phone', 'width' => 40],*/
];

$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </button>';

$config = [
    'order' => [[0, 'asc']],
    'columns' => [
        [
            'data' => 'id',
            'visible' => false
        ],

        [
            'data' => 'type'
        ],
        [
            'data' => 'size'
        ],
        [
            'data' => 'area'
        ],
        [
            'data' => 'title'
        ],
        [
            'data' => 'description'
        ],
        [
            'data' => 'created_at'
        ],
        [
            'data' => 'updated_at'
        ],
        [
            'data' => 'actionCol',
            'orderable' => false
        ]
    ],
    'paging' => true,
    'lengthMenu' => [ 10 ]
];

foreach ($entities as $i => &$u) {
    $temp = [
        'id' => $u['id'],
        'type' => $u['type'],
        'size' => $u['size'],
        'area' => $u['area'],
        'title' => $u['title'],
        'description' => $u['description'],
        'created_at' => $u['created_at']->format("d M Y h:i A"),
        'updated_at' => $u['created_at']->format("d M Y h:i A"),
        'actionCol' => '<nobr>'.$btnDetails.'</nobr>'
    ];
    $entities[$i] = $temp;
}
$config['data'] = $entities;
@endphp



@section('content')
    <x-adminlte-datatable id="tblUser" :heads="$heads" :config="$config" striped hoverable with-buttons>
        @foreach($config['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>
@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#tblUser').DataTable();
        $('#tblUser tbody').on( 'click', 'button', function () {
            var data = table.row( $(this).parents('tr') ).data();
            // window.location.href="{{ url('/properties/') }}/" + data['id'];
            window.location.href="{{ route('properties.show', '') }}" + "/" + data['id'];
        } );
    });
</script>
@stop
