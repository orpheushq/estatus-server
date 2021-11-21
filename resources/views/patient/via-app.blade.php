@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
    <h2 class="text-center display-4">Search</h2>
@stop

@section('content')


    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="get">
                <div class="input-group input-group-lg">
                    <input type="search" name="mobileNo" class="form-control form-control-lg" placeholder="Type your keywords here" value="{{ $mobileNo }}" autocomplete="off">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-lg btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!is_null($mobileNo) && is_null($patient))
    <div class="row mt-2">
        <div class="col-12">
            <p class="text-center display-5">No unassigned patients with that mobile number</p>
        </div>
    </div>
    @endif

    @if (!is_null($patient))
    <div class="row mt-4">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form method="post" action="{{ url('/patients/create-via-app').'/'.$patient->id }}">
                    <!-- Widget: user widget style 1 -->
                    <div class="card card-widget widget-user">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-info">
                            <h3 class="widget-user-username">{{ $patient->name }}</h3>
                            <h5 class="widget-user-desc">Diabetes: {{ $patient->diabetesType }} </h5>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="User Avatar">
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">Unassigned</h5>
                                        <span class="description-text">
                                            <button type="submit" class="btn btn-block btn-default btn-sm mt-2">Add to my organization<i class="fas fa-arrow-circle-right ml-1"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4"></div>
    </div>
    @endif

    <!--
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Quick Example</h3>
        </div>
        <form>
            <div class="card-body">
              <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
              </div>
              <div class="form-group">
                <label for="exampleInputFile">File input</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="exampleInputFile">
                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                  </div>
                  <div class="input-group-append">
                    <span class="input-group-text">Upload</span>
                  </div>
                </div>
              </div>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    -->
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop