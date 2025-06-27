<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabinRoom extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_room';
    public $incrementing = false; // Assuming id_room is not auto-incrementing
    protected $keyType = 'string'; // Assuming id_room is a string

    protected $fillable = [
        'id_cabin',
        'id_room',
        'typeroom',
        'description',
        'price',
        'room_photos',
        'max_guests',
        'slot_room',
        'status',
    ];

    protected $casts = [
        'room_photos' => 'array',
        'price' => 'integer',
        'max_guests' => 'integer',
        'slot_room' => 'integer',
        'status' => 'boolean',
    ];

    public function cabin()
    {
        // Ensure foreign key ('id_cabin') and local key ('id_cabin' in Cabin model) match your database schema
        return $this->belongsTo(Cabin::class, 'id_cabin', 'id_cabin');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_room', 'id_room'); // Ensure 'id_room' is used correctly
    }
}