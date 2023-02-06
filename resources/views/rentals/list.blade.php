@extends('adminlte::page')

@section('title', 'Rentals')

@section('content_header')
    <div class="row justify-content-flex-start">
        <div class="mr-2">
            <h1>Rentals</h1>
        </div>
        <div class="mr-2">
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
        'created_at' => $u->getRelation('property')['created_at']->format("d M Y h:i A"),
        'updated_at' => $u->getRelation('property')['updated_at']->format("d M Y h:i A"),
        'actionCol' => '<nobr>'.$btnDetails.'</nobr>'
    ];
    $entities[$i] = $temp;
}
$config['data'] = $entities;
@endphp



@section('content')
    <form method="post" action="{{ route('rentals.upload') }}" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card card-default collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Upload data</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <x-adminlte-input-file igroup-size="sm" name="dataFile" required accept=".xls,.xlsx"/>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input type="checkbox" name="test" class="form-check-input" id="chkTestUpload" checked>
                                    <label class="form-check-label" for="chkTestUpload">Test upload</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            <input type="submit" class="btn btn-primary" value='Upload' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
