<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class SegmentController extends Controller
{
    public function destroy(Segment $segment)
    {
        if ($segment->audio->user_id !== auth()->id()) {
            abort(403);
        }

        $segment->delete();

        ToastMagic::success('Segmento eliminado correctamente.');
        return redirect()->route('audios.show', $segment->audio_id);
    }
}