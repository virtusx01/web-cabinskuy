<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusSlotRoom extends Model
{
    use HasFactory;

    protected $table = 'status_slot_room';

    // No single auto-incrementing ID
    public $incrementing = false;

    // Define the composite primary key
    protected $primaryKey = ['id_room', 'date'];

    /**
     * Override the setKeysForSaveQuery method to handle composite primary keys.
     * This is necessary for update and delete operations.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }


    protected $fillable = [
        'id_room',
        'date',
        'total_slots',
        'occupied_slots',
        'remaining_slot_room',
    ];

    protected $casts = [
        'date' => 'date',
        'total_slots' => 'integer',
        'occupied_slots' => 'integer',
        'remaining_slot_room' => 'integer',
    ];

    /**
     * Get the cabin room that the slot status belongs to.
     */
    public function cabinRoom(): BelongsTo
    {
        return $this->belongsTo(CabinRoom::class, 'id_room', 'id_room');
    }
}