<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $fillable = [
        'user_id',
        'mesa_id',
        'fecha_reserva',
        'hora_reserva',
        'cantidad_personas',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'cantidad_personas' => 'integer',
        'fecha_reserva' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    // Mutator para el estado
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }

    // Accessor para el estado
    public function getEstadoAttribute($value)
    {
        return ucfirst($value);
    }
}
