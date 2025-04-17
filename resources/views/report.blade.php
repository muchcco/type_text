@extends('layouts.layout')

@section('main')
  <div class="report-container" style="padding: 20px; color: #fff;">
    <h2>Reporte de {{ $player->alias }}</h2>
    @if($player->scores->isEmpty())
      <p>No se han registrado puntajes aún.</p>
    @else
      <table style="width:100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
          <tr style="background: #333;">
            <th style="padding: 10px; border: 1px solid #444;">#</th>
            <th style="padding: 10px; border: 1px solid #444;">Texto</th>
            <th style="padding: 10px; border: 1px solid #444;">Ingresados (chars)</th>
            <th style="padding: 10px; border: 1px solid #444;">Tiempo (s)</th>
            <th style="padding: 10px; border: 1px solid #444;">Puntaje</th>
            <th style="padding: 10px; border: 1px solid #444;">Fecha</th>
          </tr>
        </thead>
        <tbody>
          @foreach($player->scores as $index => $score)
            <tr style="background: {{ $index % 2 ? '#222' : '#1a1a1a' }};">
              <td style="padding: 8px; border: 1px solid #444; text-align: center;">
                {{ $index + 1 }}
              </td>
              <td style="padding: 8px; border: 1px solid #444;">
                {{ \Illuminate\Support\Str::limit($score->text->content, 50) }}
              </td>
              <td style="padding: 8px; border: 1px solid #444; text-align: center;">
                {{ $score->character_count }}
              </td>
              <td style="padding: 8px; border: 1px solid #444; text-align: center;">
                {{ number_format($score->time_taken, 2) }}
              </td>
              <td style="padding: 8px; border: 1px solid #444; text-align: center;">
                {{ $score->score }}%
              </td>
              <td style="padding: 8px; border: 1px solid #444; text-align: center;">
                {{ $score->created_at->format('Y-m-d H:i:s') }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <div style="margin-top: 30px; text-align:center;">
      <a href="{{ route('game.index') }}" 
         style="padding:10px 20px; background:#555; color:#fff; border-radius:5px; text-decoration:none;">
        Jugar de Nuevo
      </a>
    </div>
  </div>
@endsection
