<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Note;
use Illuminate\Http\Request;
use Devrabiul\ToastMagic\Facades\ToastMagic;

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

        ToastMagic::success('Nota creada correctamente.');
        return redirect()->route('audios.show', $audio);;
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        ToastMagic::success('Nota eliminada correctamente.');
        return redirect()->route('audios.show', $note->audio_id);
    }
}