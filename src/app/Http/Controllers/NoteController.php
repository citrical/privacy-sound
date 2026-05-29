<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request, Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $audio->notes()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->route('audios.show', $audio)
            ->with('success', 'Nota añadida correctamente.');
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        return redirect()->route('audios.show', $note->audio_id)
            ->with('success', 'Nota eliminada correctamente.');
    }
}