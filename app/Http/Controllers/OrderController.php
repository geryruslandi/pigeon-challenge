<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'distance' => 'required|numeric|min:1',
            'deadline' => 'required|date_format:d-m-Y H:i|after_or_equal:now'
        ]);

        $order = (new OrderService($request->user()))->makeOrder($request->distance, Carbon::createFromFormat('d-m-Y H:i', $request->deadline));

        return jsonResponse([
            "order" => new OrderResource($order)
        ]);
    }
}
