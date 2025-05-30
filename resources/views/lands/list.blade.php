@extends('adminlte::page')

@section('title', 'Bare Lands')

@section('content_header')
    <div class="row justify-content-flex-start">
        <div class="mr-2">
            <h1>Bare Lands</h1>
        </div>
        <div class="mr-2">
        </div>
    </div>
@stop

@section('plugins.Datatables', true)

{{-- Setup data for datatables --}}
@php
    $heads = ['ID', 'Area', 'Title', 'Size', 'Latest Price', 'Latest Interest', 'Description', 'Created At', 'Updated At', 'Action'];

    $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </button>';

    $config = [
        'order' => [[0, 'asc']],
        'columns' => [
            [
                'data' => 'id',
                'visible' => false,
            ],
            [
                'data' => 'area',
            ],
            [
                'data' => 'title',
            ],
            [
                'data' => 'size',
            ],
            [
                'data' => 'price',
            ],
            [
                'data' => 'interest',
            ],
            [
                'data' => 'description',
            ],
            [
                'data' => 'created_at',
            ],
            [
                'data' => 'updated_at',
            ],
            [
                'data' => 'actionCol',
                'orderable' => false,
            ],
        ],
        'paging' => false,
        'info' => false,
        'data' => []
    ];
    foreach ($entities as $i => &$u) {
        $thisProperty = $u->property;
        $thisStatistic = $thisProperty->statistics->first();
        $temp = [
            'id' => $u->id,
            'area' => $thisProperty->area,
            'size' => $u->size,
            'price' => number_format($thisStatistic->price, 2),
            'interest' => number_format($thisStatistic->interest, 0),
            'title' => $thisProperty->title,
            'description' => $thisProperty->description,
            'created_at' => $thisProperty->created_at->format('d M Y h:i A'),
            'updated_at' => $thisProperty->updated_at->format('d M Y h:i A'),
            'actionCol' => '<nobr>' . $btnDetails . '</nobr>',
        ];
        $config['data'][] = $temp;
    }
@endphp



@section('content')
    <form method="post" action="{{ route('lands.upload') }}" enctype="multipart/form-data">
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
                        <div class="container-fluid">
                            <x-adminlte-callout theme="warning">Make sure there are no uploads currently running!</x-adminlte-callout>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <x-adminlte-input-file igroup-size="sm" name="dataFile" required accept=".xls,.xlsx,.csv" />
                            </div>
                            <div class="col-2">
                                <x-adminlte-select name="sourceSelect" igroup-size="sm" required>
                                    <option value="" selected disabled>(Select)</option>
                                    <option value="ikman">ikman.lk</option>
                                    <option value="lpw">LankaPropertyWeb</option>
                                    <option value="hitad">Hitad.lk</option>
                                </x-adminlte-select>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input type="checkbox" name="test" class="form-check-input" id="chkTestUpload"
                                        checked>
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
    <form method="post" action="{{ route('lands.triggerAlertNewProperties') }}" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card card-default collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Alert New Properties</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <x-adminlte-input name="minDate" label="Minimum date" type="date">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <div class="form-check">
                                    <input type="checkbox" name="dryRun" class="form-check-input" id="chkDryRun" checked>
                                    <label class="form-check-label" for="chkDryRun">Dry run</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            <input type="submit" class="btn btn-primary" value='Trigger' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-adminlte-datatable id="tblLand" :heads="$heads" :config="$config" striped hoverable with-buttons>
        @foreach ($config['data'] as $row)
            <tr>
                @foreach ($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>

    @if ($entities->count())
    {{ $entities->links('pagination::bootstrap-5') }}
    @endif
@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#tblLand').DataTable();
            $('#tblLand tbody').on('click', 'button', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ route('lands.show', '') }}" + "/" + data['id'];
            });
        });
    </script>
@stop
