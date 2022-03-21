<?php

use Illuminate\Support\Facades\Hash;

function jsonResponse(Array $payload, int $status = 200) {
    return response()->json([
        "data" => $payload
    ], $status);
}

function jsonResponseSuccess() {
    return jsonResponse([
        "message" => "success"
    ]);
}

function generatePassword(String $password){
    return Hash::make($password);
}
