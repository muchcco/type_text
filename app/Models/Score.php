<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = ['player_id', 'text_id', 'score', 'time_taken', 'character_count'];


    // Cada puntaje pertenece a un jugador
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    // Cada puntaje pertenece a un texto
    public function text()
    {
        return $this->belongsTo(Text::class);
    }
}
