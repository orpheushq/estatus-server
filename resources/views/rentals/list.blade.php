@extends('adminlte::page')

@section('title', 'Properties')

@section('content_header')
    <div class="row justify-content-flex-start">
        <div class="mr-2">
            <h1>Rentals</h1>
        </div>
        <div class="mr-2">
            <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Area',
    'Title',
    'Rooms',
    'Bathrooms',
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
            'data' => 'area'
        ],
        [
            'data' => 'title'
        ],
        [
            'data' => 'rooms'
        ],
        [
            'data' => 'bathrooms'
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
        'area' => $u->getRelation('property')['area'],
        'rooms' => $u['rooms'],
        'bathrooms' => $u['bathrooms'],
        'title' => $u->getRelation('property')['title'],
        'description' => $u->getRelation('property')['description'],
        'created_at' => $u['created_at']->format("d M Y h:i A"),
        'updated_at' => $u['updated_at']->format("d M Y h:i A"),
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
            window.location.href="{{ route('rentals.show', '') }}" + "/" + data['id'];
        } );
    });
</script>
@stop
