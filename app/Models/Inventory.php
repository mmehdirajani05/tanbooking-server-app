<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'date',
        'total_rooms',
        'available_rooms',
    ];

    protected function casts(): array
    {
        return [
            'date'           => 'date',
            'total_rooms'    => 'integer',
            'available_rooms'=> 'integer',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
