@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Organization',
    'Name',
    'Email',
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
            'data' => 'organizationName'
        ], 
        [
            'data' => 'name'
        ],
        [
            'data' => 'email'
        ],
        [
            'data' => 'actionCol',
            'orderable' => false
        ]
    ],
    'paging' => true,
    'lengthMenu' => [ 10 ]
];

foreach ($users as $i => &$u) {
    $temp = [
        'id' => $u['id'],
        'organizationName' => $u->organization()->first()->name,
        'name' => $u['name'],
        'email' => $u['email'],
        'actionCol' => '<nobr>'.$btnDetails.'</nobr>'
    ];
    $users[$i] = $temp;
}
$config['data'] = $users;
@endphp



@section('content')
    <x-adminlte-datatable id="tblUser" :heads="$heads" :config="$config" theme="light" striped hoverable with-buttons>
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
            // window.location.href="{{ url('/users/') }}/" + data['id'];
            window.location.href="{{ route('users.show', '') }}" + "/" + data['id'];
        } );
    });
</script>
@stop