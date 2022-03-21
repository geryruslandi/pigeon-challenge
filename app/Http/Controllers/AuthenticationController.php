<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request) {

        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $customer = Customer::firstWhere('username', $request->username);


        if(!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'username' => ['Provided Credentials are incorrect'],
            ]);
        }

        return jsonResponse([
            "token" => $customer->createToken('authentication')->plainTextToken
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return jsonResponseSuccess();
    }
}
