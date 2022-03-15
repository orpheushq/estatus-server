@extends('adminlte::page')

@section('title', 'Patients')

@section('content_header')
@stop

@section('content_top_nav_left')
    <li class="nav-item">
        <a class="nav-link">{{ $patient->id === -1 ? "Add new patient": $patient->name }}</a>
    </li>
@stop

@section('content')

    @if ($patient->id !== -1)
        <x-navtab :tabs="[
            ['link' => 'info', 'text' => 'Information'],
            ['link' => 'logbook', 'text' => 'Logbook'],
            ['link' => 'health', 'text' => 'Health data'],
            ['link' => 'remarks', 'text' => 'Remarks'],
            ['link' => 'about', 'text' => 'About patient']
        ]"/>
    @endif

    <form method="post">
        @csrf
        <h2>Personal Information</h2>
        <div class="d-flex flex-sm-row flex-column">
            <div class="flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="txtName" class="form-label required">Full name</label>
                <input type="text" class="form-control" id="txtName" name="name" value="{{ $patient->name }}" required>
            </div>
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="selGender" class="form-label required">Gender</label>
                <select class="form-control" id="selGender" name="gender">
                    @foreach (Config::get("constants.genders") as $e)
                        <option {{ $patient->gender === $e ? "selected": ""}}>{{ $e }}</option>    
                    @endforeach
                </select>
            </div>
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="selDiabetes" class="form-label required">Type of diabetes</label>
                <select class="form-control" id="selDiabetes" name="diabetestype">
                    @foreach (Config::get("constants.diabetesTypes") as $e)
                        <option {{ $patient->diabetesType === $e ? "selected": ""}}>{{ $e }}</option>    
                    @endforeach
                </select>
            </div>
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="inpDob" class="form-label required">Date of birth</label>
                <input type="date" class="form-control" id="inpDob" name="dob" value="{{ $patient->dob }}" required>
            </div>
        </div>

        <div class="d-flex flex-sm-row flex-column">
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="txtPatientNo" class="form-label required">Patient number</label>
                <input type="number" class="form-control" id="txtPatientNo" name="patientNo" value="{{ $patient->patientNo }}" required>
            </div>
        </div>

        <h2>Advanced Information</h2>
        <div class="d-flex flex-sm-row flex-column">
            @can('external patients')
                <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                    <label for="inpOrganizationId" class="form-label required">Organization</label>
                    <select class="form-control" id="selOrganization" name="organizationId">
                        @foreach ($organizations as $i => $e)
                            <option {{ ( isset($patient->organizationId) ? $patient->organizationId: Auth::user()->organizationId ) == $e['id'] ? "selected": ""}} value={{ $e['id'] }}>{{ $e['name'] }}</option>    
                        @endforeach
                    </select>
                </div>
            @endcan
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="inpDiagnosisDate" class="form-label required">Date of diagnosis</label>
                <input type="date" class="form-control" id="inpDiagnosisDate" name="diagnosisDate" value="{{ $patient->diagnosisDate }}">
            </div>
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="nmbMobile" class="form-label required">Mobile phone</label>
                <input type="number" class="form-control" id="nmbMobile" name="mobileNo" required value="{{ $patient->mobileNo }}">
            </div>
        </div>
        <h3 class="h5">Emergency contact</h3>
        <div class="d-flex flex-sm-row flex-column">
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="txtEmergencyName" class="form-label required">Person name</label>
                <input type="text" class="form-control" id="txtEmergencyName" name="emergencyContactName" value="{{ $patient->emergencyContactName }}">
            </div>
            <div class="d-flex flex-column me-3 mb-2 col-sm-3 col-12">
                <label for="nmbEmergencyMobile" class="form-label required">Mobile number</label>
                <input type="text" class="form-control" id="nmbEmergencyMobile" name="emergencyContactMobile" value="{{ $patient->emergencyContactMobile }}">
            </div>
        </div>
        <div class="d-flex col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>

    </form>
    
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop