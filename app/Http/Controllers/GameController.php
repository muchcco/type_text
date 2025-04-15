<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Text;
use App\Models\Player;
use App\Models\Score;

class GameController extends Controller
{
    // Muestra la vista principal del juego
    public function index()
    {
        return view('game');
    }

    // Retorna un texto aleatorio en formato JSON
    public function getRandomText()
    {
        // Obtenemos un registro aleatorio
        $text = Text::inRandomOrder()->first();
        return response()->json($text);
    }

    // Registra o consulta un jugador por alias
    public function registerPlayer(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|max:50'
        ]);

        // Busca o crea el jugador según el alias
        $player = Player::firstOrCreate(['alias' => $request->alias]);
        return response()->json($player);
    }

    // Almacena el puntaje obtenido por el jugador
    public function storeScore(Request $request)
    {
        $request->validate([
            'player_id'       => 'required|exists:players,id',
            'text_id'         => 'required|exists:texts,id',
            'score'           => 'required|integer',
            'time_taken'      => 'required|numeric',
            'character_count' => 'required|integer'
        ]);

        $score = Score::create($request->all());
        return response()->json($score);
    }

}
