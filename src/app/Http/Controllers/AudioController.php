<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioController extends Controller
{
    public function index()
    {
        $audios = auth()->user()->audios()->latest()->get();

        return view('audios.index', compact('audios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audio' => [
                'required',
                'file',
                'mimes:mp3,wav,ogg,m4a,flac',
                'max:65536',
            ],
        ]);

        $file = $request->file('audio');
        $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('audios')->put($storedFilename, file_get_contents($file));

        auth()->user()->audios()->create([
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $storedFilename,
            'mime_type'         => $file->getMimeType(),
            'size'              => $file->getSize(),
            'status'            => 'pending',
        ]);

        return redirect()->route('audios.index')
            ->with('success', '¡Audio subido correctamente!');
    }

    public function destroy(Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('audios')->delete($audio->stored_filename);

        if ($audio->processed_filename) {
            Storage::disk('audios')->delete($audio->processed_filename);
        }

        $audio->delete();

        return redirect()->route('audios.index')
            ->with('success', 'Audio eliminado correctamente.');
    }

    public function stream(Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $path = Storage::disk('audios')->path($audio->stored_filename);

        return response()->file($path, [
            'Content-Type' => $audio->mime_type,
        ]);
    }

    public function show(Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        return view('audios.show', compact('audio'));
    }
}