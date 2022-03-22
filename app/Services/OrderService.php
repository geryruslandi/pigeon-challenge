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
        $assignedPigeon->order_cycle_count = $assignedPigeon->order_cycle_count >= Order::CYCLE_COUNT_NEEDED_TO_TAKE_A_REST ? 0 : $assignedPigeon->order_cycle_count + 1;
        $assignedPigeon->previous_finished_order_time = now();
        $assignedPigeon->save();

        //TODO dispatch invoice email with order detail and its cost
    }

    /**
     * Make order
     * For now just choose fastest available pigeon
     *
     * @param  mixed $distance
     * @param  mixed $deadline
     * @return Customer
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
            'cost' => $distance * $fastestPigeon->cost_per_distance,
            'deadline' => $deadline,
            'assigned_pigeon_id' => $fastestPigeon->id,
            'status' => Order::STATUS_ON_GOING
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
                            $query->where('order_cycle_count', '<', Order::CYCLE_COUNT_NEEDED_TO_TAKE_A_REST);
                            // where is time for rest and already take enough rest
                            $query->orWhere(function($query) {
                                $query->where('order_cycle_count', '>=', Order::CYCLE_COUNT_NEEDED_TO_TAKE_A_REST)
                                    ->whereRaw("DATE_ADD(previous_finished_order_time, INTERVAL downtime HOUR) <= now()");
                            });
                        });

                })
                ->orderBy('speed_per_hour', 'desc')
                ->first();

        return $pigeon;
    }
}
