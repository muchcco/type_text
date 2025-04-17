<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Text;
use App\Models\Player;
use App\Models\Score;
use Carbon\Carbon;

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

        // $score = Score::create($request->all());
        $scores = new Score;
        $scores->player_id = $request->player_id;
        $scores->text_id = $request->text_id;
        $scores->character_count = $request->character_count;
        $scores->score = $request->score;
        $scores->time_taken = $request->time_taken;
        $scores->time_first = $request->time_first;
        $scores->time_end = Carbon::now()->format('h:m:s');
        $scores->save();

        return response()->json($scores);
    }

    public function report($playerId)
    {
        // Carga el jugador junto con sus puntajes y el texto relacionado
        $player = Player::with(['scores.text'])->findOrFail($playerId);

        return view('report', compact('player'));
    }

    /******** MODAL SECTION  **************************/

    public function modalNextPlayer(Request $request)
    {
        $view = view('partials.next-player-modal')->render();

        return response()->json(["html" => $view]);
    }

}
