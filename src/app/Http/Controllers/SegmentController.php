<?php

namespace App\Http\Controllers;

use App\Models\Segment;

class SegmentController extends Controller
{
    public function destroy(Segment $segment)
    {
        if ($segment->audio->user_id !== auth()->id()) {
            abort(403);
        }

        $segment->delete();

        return redirect()->route('audios.show', $segment->audio_id)
            ->with('success', 'Segmento eliminado correctamente.');
    }
}