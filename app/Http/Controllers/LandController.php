<?php

namespace App\Http\Controllers;

use App\Classes\CliProcess;
use App\Classes\ProcLand;
use App\Classes\ProcRental;
use App\Models\Land;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandController extends Controller
{

    public function triggerAlertNewProperties(Request $request): RedirectResponse
    {
        $dryRun = !is_null($request->post('dryRun'));
        $minDate = !is_null($request->post('minDate')) ? $request->post('minDate') : '';

        $params = [];
        if ($dryRun) {
            $params[] = '-D';
        }
        if ($minDate !== '') {
            $params[] = $minDate;
        }

        CliProcess::startBgProcess('alert:newProperties', $params);

        return redirect()->back();
    }
    public function upload(Request $request)
    {
        $thisFile = $request->file('dataFile');
        $path = $thisFile->store('land-upload');
        $dryRun = !is_null($request->post('test'));
        $dataSource = $request->input('sourceSelect');

        Log::channel('upload')->notice("Lands CSV file uploaded to ${path}");

        $params = [$path, $dataSource];
        if ($dryRun) {
            $params[] = '-D';
        }
        CliProcess::startBgProcess('process:lands', $params);

        return redirect()->back();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $values = [];
        $validations = [];
        $permissions = [];
        $validator = null;
        $isValid = FALSE;

        $lands = Land::with([
            "property.statistics" => function (HasMany $query) {
                $query->latest();
            }
        ])->where('id', '>', 0)->orderBy('created_at', 'desc')->paginate(25);
        $values = $request->all();
        unset($values['_token']);

        // Add validation rules
        $isValid = TRUE;

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response([ "errors" => $validator->errors() ], 422);
            } else {
                return response($lands->get(), 200);
            }
        } else {
            if (!$isValid) {
                return redirect()->back()->withErrors($validator);
            } else {
                return view('lands.list', ["entities" => $lands]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
