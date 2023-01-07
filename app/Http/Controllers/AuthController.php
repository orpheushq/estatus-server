<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {}

    public function gauth(Request $request) {
        /**
         * Authenticate using OAuth (Google)
         */
        $gclient = new \Google_Client();

        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);

        $ticket = $gclient->verifyIdToken($request->token);

        if ($ticket) {
            $email = $ticket["email"];

            if ($email !== $request->email) {
                return response([ "error" => "email_mismatch" ], 403);
            } else {
                $user = User::where('email', $request->email)->first();

                if (!$user) {
                    // user does not exist
                    $newUser = User::create([
                        'name' => $ticket["name"] ?? $request->email,
                        'organizationId' => config("constants.defaultOrganization"), // NOTE: for multi-organizational apps, this cannot be hardcoded!
                        'email' => $request->email,
                        'password' => Hash::make(Str::random(10)),
                    ]);
                    $newUser->assignRole(config("constants.defaultRole"));
                }

                return $this->login($request, TRUE); // continue login flow
            }
        } else {
            return response([ "error" => "faulty_token" ], 403);
        }
    }

    public function login(Request $request, $isOAuth = FALSE) {
        $user = null;
        if (!$isOAuth) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
        } else {
            // login continuation from a OAuth flow
            $user = User::where('email', $request->email)->first();
        }

        $token = $user->createToken($request->device_name ?? "loremipsumdevice")->plainTextToken;
        $response = [
            'user' => $this->verifyToken($request, $user),
            'token' => $token
        ];
        return response($response, 200);
    }
    public function verifyToken(Request $request, $user = NULL) {
        $thisUser = $request->user() ?? $user;
        $permissions = [];
        foreach ($thisUser->getAllPermissions() as $p) {
            $permissions[] = $p->name;
        }
        unset($thisUser['permissions']);
        $thisUser['permissions'] = $permissions;
       

        unset($thisUser['roles']);

        return $thisUser;
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response(["message" => "success"], 200);
    }
}