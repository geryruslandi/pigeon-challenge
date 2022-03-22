<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Pigeon;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class OrderService {

    private Order $order;

    function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function markAsFinished()
    {
        $this->order->status = Order::STATUS_FINISHED;
        $this->order->finished_time = now();
        $this->order->save();

        $assignedPigeon = $this->order->assignedPigeon;
        $assignedPigeon->order_cycle_count = $assignedPigeon->order_cycle_count >= 2 ? 0 : $assignedPigeon->order_cycle_count + 1;
        $assignedPigeon->previous_finished_order_time = now();
        $assignedPigeon->save();

        //TODO dispatch invoice email
    }

    /**
     * Make order
     * For now just choose fastest available pigeon
     *
     * @param  mixed $distance
     * @param  mixed $deadline
     * @return void
     */
    public static function makeOrder(Customer $customer, int $distance, Carbon $deadline)
    {
        $fastestPigeon = self::getFastestAvailablePigeon($distance, $deadline);

        if(!$fastestPigeon){
            throw ValidationException::withMessages([
                'deadline' => ['No pigeons available with choosed distance and deadline']
            ]);
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'distance' => $distance,
            'deadline' => $deadline,
            'assigned_pigeon_id' => $fastestPigeon->id
        ]);

        return $order;
    }

    private static function getFastestAvailablePigeon(int $distance, Carbon $deadline)
    {
        $deadlineInHours = now()->diffInHours($deadline);
        $minimumSpeed = $distance / $deadlineInHours;

        $pigeon = Pigeon::where('maximum_range', '>=', $distance)
                ->where('speed_per_hour', '>=', $minimumSpeed)
                ->where(function($query) {
                    // where pigeon is not delivering
                    $query->whereDoesntHave('orders', function($query) {
                            $query->where('status', Order::STATUS_ON_GOING);
                        })
                        ->where(function($query) {
                            // where is not the time for rest
                            $query->where('order_cycle_count', '<', 2);
                            // where is time for rest and already take enough rest
                            $query->orWhere(function($query) {
                                $query->where('order_cycle_count', '>=', 2)
                                    ->whereRaw("previous_finished_order_time <= DATE_SUB(now(), INTERVAL downtime HOUR)");
                            });
                        });

                })
                ->orderBy('speed_per_hour', 'desc')
                ->first();

        return $pigeon;
    }
}
