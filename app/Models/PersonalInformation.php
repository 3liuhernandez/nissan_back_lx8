<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'adress',
        'aerial_aeroline',
        'aerial_arrive_time',
        'aerial_booking',
        'aerial_departure_time',
        'aerial_destination',
        'aerial_flight',
        'birthdate',
        'bus_arrive_time',
        'bus_booking',
        'bus_departure_time',
        'document',
        'food',
        'parking_car_model',
        'parking_patent',
        'size',
        'tel',
        'transport',
        'registered',
    ];

    /**
     * Obtiene la relación con usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
