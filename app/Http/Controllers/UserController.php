<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
            return $request->user();
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
        $validations["currentPassword"] = [ "required", "current_password" ];
        $validations["newPassword"] = [ "required", "different:currentPassword", Password::defaults() ];

        $validator = Validator::make($values, $validations);
        $isValid = !$validator->fails();

        if ($isValid) {
            // validation has not failed; add business logic
            $thisUser = $request->user();
            foreach ($values as $k => $v) {
                if (!is_null($v)) {
                    if ($k === "password" || $k === "newPassword") {
                        $thisUser['password'] = Hash::make($values['newPassword']);
                    } else if ($k === "password_confirmation" || $k === "currentPassword") {
                        // ignore these fields
                    } else {
                        $thisUser[$k] = $v;
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
