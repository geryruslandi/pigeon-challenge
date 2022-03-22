<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'distance' => 'required|numeric|min:1',
            'deadline' => 'required|date_format:d-m-Y H:i|after_or_equal:now'
        ]);

        $order = OrderService::makeOrder($request->user(), $request->distance, Carbon::createFromFormat('d-m-Y H:i', $request->deadline));

        return jsonResponse([
            "order" => new OrderResource($order)
        ]);
    }

    public function finish(Request $request, $orderId) {

        /** @var Order */
        $order = Order::where('status', Order::STATUS_ON_GOING)
            ->where('customer_id', $request->user()->id)
            ->where('id', $orderId)
            ->firstOrFail();

        $order->markAsFinished();

        return jsonResponseSuccess();
    }
}
