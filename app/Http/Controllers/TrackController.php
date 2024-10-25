<?php

namespace App\Http\Controllers;

use App\Models\Week;
use App\Models\Track;
use App\Players\Player;
use App\Rules\PlayerUrl;
use App\Services\UserService;
use App\Exceptions\PlayerException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Category;

class TrackController extends Controller
{
    /**
     * Show given track.
     */
    public function show(Request $request, Week $week, Track $track, Player $player): View
    {
        return view('app.tracks.show', [
            'week' => $week->loadCount('tracks'),
            'track' => $track->load('category')->loadCount('likes'),
            'tracks_count' => $week->tracks_count,
            'position' => $week->getTrackPosition($track),
            'liked' => $request->user()->likes()->whereTrackId($track->id)->exists(),
            'embed' => $player->embed($track->player, $track->player_track_id),
        ]);
    }

    /**
     * Show create track form.
     */
    public function create(UserService $user): View
    {
        return view('app.tracks.create', [
            'week' => Week::current(),
            'remaining_tracks_count' => $user->remainingTracksCount(),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Create a new track.
     */
    public function store(Request $request, Player $player): RedirectResponse
    {
        $this->authorize('create', Track::class);
    
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', new PlayerUrl()],
            'category_id' => ['required', 'exists:categories,id'],
        ]);
    
        DB::beginTransaction();
    
        $track = Track::with('category')->findOrFail($id);
                
        $track->user()->associate($request->user());
        $track->week()->associate(Week::current());
    
        try {
            $details = $player->details($track->url);
    
            $track->player = $details->player_id;
            $track->player_track_id = $details->track_id;
            $track->player_thumbnail_url = $details->thumbnail_url;
    
            $track->category_id = $validated['category_id'];
    
            $track->save();
    
            DB::commit();
        } catch (PlayerException $th) {
            DB::rollBack();
            throw $th;
        }
    
        return view('tracks.show', compact('track'));

    }

    /**
     * Toggle like.
     */
    public function like(Request $request, Week $week, Track $track): RedirectResponse
    {
        $user = $request->user();

        $track->likes()->toggle([
            $user->id => ['liked_at' => now()]
        ]);

        return redirect()->route('app.tracks.show', [
            'week' => $week->uri,
            'track' => $track,
        ]);
    }

    
}
