<?php

namespace App\Http\Controllers;

use App\Classes\SmsClient;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $smsClient = null;
    public function __construct()
    {
        $this->smsClient = new SmsClient();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $isApi = FALSE)
    {
        //
        $entities = [];

        $filters = json_decode($request->filters, TRUE);

        if (is_null($filters)) {
            $entities = User::whereNotNull("organizationId")->get(); //get only assigned patients
        } else {
            /**
             * TODO: apply filters
             */

        }

        //apply permission constraits
        if (!$request->user()->hasPermissionTo("external users") && $request->user()->hasPermissionTo("internal users")) {
            $entities = User::where("organizationId", "=", $request->user()->organizationId)->get();

        } else if ($request->user()->hasPermissionTo("external users") && $request->user()->hasPermissionTo("internal users")) {
            $entities = User::where("organizationId", "<>", NULL)->get();
        } else if ($request->user()->hasPermissionTo("external users")) {
            $entities = User::where("organizationId", "<>", $request->user()->organizationId)->get();
        }
        /**
         * TODO: apply heirarchy permissions - branch admin can only see clients; super-admin can see clients and admins
         */

        if ($isApi) {
            return response($entities, 200);
        } else {
            return view('users.list', [ "users" => $entities]);
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
    public function show(Request $request, $id)
    {
        //
        if ($id === 'me') {
            $user = $request->user();
            $parsed_alert_regions = json_decode($user->alert_regions);
            return [...($user->toArray()), 'alert_regions' => is_null($parsed_alert_regions) ? null : $parsed_alert_regions];
        }
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
        $values = [];
        $validations = [];
        $permissions = [];
        $isValid = FALSE;

        $values = $request->all();
        unset($values['_token']);

        // Add validation rules
        $validations["newPassword"] = [ "different:currentPassword", Password::defaults() ];
        $validations["currentPassword"] = Rule::requiredIf(array_key_exists('newPassword', $values));

        $validator = Validator::make($values, $validations);
        $isValid = !$validator->fails();

        if ($isValid) {
            // validation has not failed; add business logic
            $thisUser = $request->user();
            foreach ($values as $k => $v) {
                if (!is_null($v)) {
                    switch ($k) {
                        case 'password': {
                            $thisUser['password'] = Hash::make($values['newPassword']);
                            break;
                        }
                        case "newPassword":
                        case "currentPassword":
                        case "password_confirmation": {
                            break;
                        }
                        case "phone_number": {
                            if ($v !== $thisUser['phone_number']) {
                                // INFO: user has updated phone number
                                $this->smsClient->sendSms($v, 'Thanks for subscribing to alerts from estates.lk');
                            }
                            $thisUser[$k] = $v;
                            break;
                        }
                        case "alert_regions":
                        default: {
                            $thisUser[$k] = $v;
                            break;
                        }
                    }
                }
            }
            $thisUser->save();
        }

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response([ "errors" => $validator->errors() ], 422);
            } else {
                return response([ "message" => "success" ], 200);
            }
        } else {
            if (!$isValid) {
                return redirect()->back()->withErrors($validator);;
            } else {
                // TODO: render view
            }
        }
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
