@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
    <h1>Patients</h1>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Name',
    'Diabetes Type',
    'Action'
    /*['label' => 'Phone', 'width' => 40],*/
];

$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </button>';

$config = [
    'order' => [[1, 'asc']],
    'columns' => [
        [
            'data' => 'id',
            'visible' => false
        ],
        [
            'data' => 'name'
        ], 
        [
            'data' => 'diabetesType'
        ],
        [
            'data' => 'actionCol',
            'orderable' => false
        ]
    ],
    'paging' => true,
    'lengthMenu' => [ 10 ]
];

foreach ($patients as $i => &$p) {
    $temp = [
        'id' => $p['id'],
        'name' => $p['name'],
        'diabetesType' => $p['diabetesType'],
        'actionCol' => '<nobr>'.$btnDetails.'</nobr>'
    ];
    $patients[$i] = $temp;
}
$config['data'] = $patients;
@endphp



@section('content')
    <x-adminlte-datatable id="tblPatient" :heads="$heads" :config="$config" theme="light" striped hoverable with-buttons>
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
        var table = $('#tblPatient').DataTable();
        $('#tblPatient tbody').on( 'click', 'button', function () {
            var data = table.row( $(this).parents('tr') ).data();
            window.location.href="{{ url('/patients/') }}/" + data['id'] + "/info";
        } );
    });
</script>
@stop