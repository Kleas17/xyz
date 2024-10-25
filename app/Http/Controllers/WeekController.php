<?php

namespace App\Http\Controllers;

use App\Models\Week;

class WeekController extends Controller
{
    /**
     * Redirige vers la semaine actuelle.
     */
    public function index()
    {
        return redirect()->route('app.weeks.show', [
            'week' => Week::current()
        ]);
    }

    /**
     * Affiche la semaine donnée.
     */
    public function show(Week $week)
    {
        return view('app.weeks.show', [
            'week' => $week->loadCount('tracks'),
            'isCurrent' => $week->toPeriod()->contains(now()),
            'tracks' => $week->tracks()
                ->with('category') 
                ->withCount('likes')
                ->ranking()
                ->get()
        ]);
    }
}