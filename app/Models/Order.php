<?php

namespace App\Models;

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

    public function assignedPigeon()
    {
        return $this->belongsTo(Pigeon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
