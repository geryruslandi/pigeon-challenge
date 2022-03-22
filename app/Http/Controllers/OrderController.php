<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'distance' => 'required|numeric|min:1',
            'deadline' => 'required|date_format:d-m-Y H:i|after_or_equal:now'
        ]);

        return jsonResponseSuccess();
    }
}
