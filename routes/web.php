<?php
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'index'])->name('game.index');

// Rutas para la lógica del juego (pueden usarse con AJAX desde el frontend)
Route::get('/api/text/random', [GameController::class, 'getRandomText'])->name('game.randomText');
Route::post('/api/player', [GameController::class, 'registerPlayer'])->name('game.registerPlayer');
Route::post('/api/score', [GameController::class, 'storeScore'])->name('game.storeScore');

Route::get('/player-modal', function () {
    return view('partials.player-modal');
})->name('player.modal');


Route::post('/next-player-modal',  [GameController::class, 'modalNextPlayer'])->name('next.modal');
Route::get('/report/{player}', [GameController::class, 'report'])
     ->name('game.report');
