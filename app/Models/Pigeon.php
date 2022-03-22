<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pigeon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'speed_per_hour',
        'maximum_range',
        'cost_per_distance',
        'downtime',
        'order_cycle_count',
        'previous_finished_order_time'
    ];

    protected $casts = [
        'previous_finished_order_time' => 'datetime',
    ];

    public function orders() {
        return $this->hasMany(Order::class, 'assigned_pigeon_id');
    }
}
