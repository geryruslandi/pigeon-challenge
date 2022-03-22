<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'distance' => $this->distance,
            'deadline' => $this->deadline,
            'assigned_pigeon_id' => $this->assigned_pigeon_id,
            'finished_time' => $this->finished_time,
            'status' => $this->status
        ];
    }
}
