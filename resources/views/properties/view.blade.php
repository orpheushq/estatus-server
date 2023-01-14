@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Properties')

@section('content_header')
@stop


@section('content_top_nav_left')
    <li class="nav-item">
        <a class="nav-link">{{ is_null($entity['id']) ? "Add Property": "Edit Property"  }}</a>
    </li>
@stop

@section('content')

    <form method="{{ is_null($entity->id) ? "post": "get" }}" action="{{ is_null($entity->id) ? route('properties.store'): route('properties.edit', $entity->id) }}" class="pt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ is_null($entity['id']) ? "Add Property": "Edit Property" }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                @php
                                    $config = [
                                        "placeholder" => "Select an option or add new one...",
                                        "tags" => true
                                    ];
                                @endphp
                                <x-adminlte-select2 id="selType" name="type" label="Type" :config="$config" required>
                                    <option/>
                                    @foreach($types as $t)
                                        <option {{ $entity['type'] === $t ? "selected": "" }}>{{ ucfirst($t) }}</option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                            <div class="col-12 col-md-6">
                                <x-adminlte-select2 id="selArea" name="area" label="Area" :config="$config" required>
                                    <option/>
                                    @foreach($areas as $t)
                                        <option {{ $entity['area'] === $t ? "selected": "" }}>{{ $t }}</option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                            <div class="col-12 col-md-6">
                                <x-adminlte-input name="title" label="Title" type="text" value="{{ $entity['title'] }}" required />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-adminlte-input name="size" label="Size" type="number" step="any" value="{{ $entity['size'] }}" required />
                            </div>
                            <div class="col-12">
                                <x-adminlte-textarea name="description" label="Description" required>
                                    {{ $entity['description'] }}
                                </x-adminlte-textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <x-adminlte-input name="url" label="URL" type="text" value="{{ $entity['url'] }}" required />
                            </div>
                            <div class="col-6 col-md-3">
                                <x-adminlte-input name="latitude" label="Latitude" type="number" step="any" value="{{ $entity['location']?->latitude }}" required />
                            </div>
                            <div class="col-6 col-md-3">
                                <x-adminlte-input name="longitude" label="Longitude" type="number" step="any" value="{{ $entity['location']?->longitude }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            <input type="submit" class="btn btn-primary" value='{{ is_null($entity['id']) ? "Add": "Update" }}' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
