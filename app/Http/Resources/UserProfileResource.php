<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'email'            => $this->email,
            'age'              =>$this->age,
            'phone'            => $this->phone,
            'telegram_id'      => $this->telegram_id,
            'role'             => $this->role->name,
            'teams'            => $this->teams->pluck('name'),
            'country'          => $this->country,
            'job_field'        => $this->job_field,
            'experience_years' => $this->experience_years,
            'weekly_hours'     => $this->weekly_hours,
            'status'           => $this->status ? 'active' : 'inactive',
            'joined_at'        => $this->created_at->format('Y-m-d'),
            'last_update'      => $this['updated_at']->format('Y-m-d'),
        ];
    }
}
