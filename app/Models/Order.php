<?php

namespace App\Models;

use App\Services\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on_going';
    const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'customer_id',
        'distance',
        'deadline',
        'assigned_pigeon_id',
        'finished_time',
        'status'
    ];

    protected $casts = [
        'deadline' => 'date',
        'finished_time' => 'date',
    ];

    public function assignedPigeon()
    {
        return $this->belongsTo(Pigeon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function markAsFinished()
    {
        (new OrderService($this))->markAsFinished();

        return $this;
    }
}
