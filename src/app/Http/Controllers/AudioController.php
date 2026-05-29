<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function process(Request $request, Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'regions' => ['required', 'json'],
        ]);

        $regions = json_decode($request->regions, true);

        if (empty($regions)) {
            return redirect()->route('audios.show', $audio)
                ->with('error', 'Debes marcar al menos un segmento.');
        }

        // Guardar segmentos
        $audio->segments()->delete();
        foreach ($regions as $region) {
            $audio->segments()->create([
                'start' => $region['start'],
                'end'   => $region['end'],
            ]);
        }

        // Rutas de ficheros
        $inputPath      = Storage::disk('audios')->path($audio->stored_filename);
        $outputFilename = 'processed_' . $audio->stored_filename;
        $outputPath     = Storage::disk('audios')->path($outputFilename);

        // Construir filtro FFmpeg con beep
        $silenceFilters = [];
        foreach ($regions as $region) {
            $start = round($region['start'], 3);
            $end   = round($region['end'], 3);
            $silenceFilters[] = "volume=enable='between(t,{$start},{$end})':volume=0";
        }
        $silenceChain = implode(',', $silenceFilters);

        $beepFilters = [];
        foreach ($regions as $index => $region) {
            $start    = round($region['start'], 3);
            $end      = round($region['end'], 3);
            $duration = round($end - $start, 3);
            $delayMs  = round($start * 1000);
            $beepFilters[] = "sine=frequency=1000:duration={$duration},adelay={$delayMs}|{$delayMs}[beep{$index}]";
        }

        $filterComplex  = '[0:a]' . $silenceChain . '[silenced];';
        $filterComplex .= implode(';', $beepFilters) . ';';
        $inputs         = '[silenced]' . implode('', array_map(fn($i) => "[beep{$i}]", array_keys($regions)));
        $filterComplex .= $inputs . 'amix=inputs=' . (count($regions) + 1) . ':normalize=0[out]';

        $command = sprintf(
            'ffmpeg -y -i %s -filter_complex "%s" -map "[out]" %s 2>&1',
            escapeshellarg($inputPath),
            $filterComplex,
            escapeshellarg($outputPath)
        );

        // Ejecutar FFmpeg
        $audio->update(['status' => 'processing']);

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $audio->update(['status' => 'failed']);
            return redirect()->route('audios.show', $audio)
                ->with('error', 'Error al procesar el audio.');
        }

        $audio->update([
            'status'             => 'processed',
            'processed_filename' => $outputFilename,
        ]);

        return redirect()->route('audios.show', $audio)
            ->with('success', '¡Audio procesado correctamente!');
    }

    public function streamProcessed(Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$audio->processed_filename) {
            abort(404);
        }

        $path = Storage::disk('audios')->path($audio->processed_filename);

        return response()->file($path, [
            'Content-Type' => $audio->mime_type,
        ]);
    }

    public function download(Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$audio->processed_filename) {
            abort(404);
        }

        $path = Storage::disk('audios')->path($audio->processed_filename);
        $downloadName = 'processed_' . $audio->original_filename;

        return response()->download($path, $downloadName, [
            'Content-Type' => $audio->mime_type,
        ]);
    }

    public function update(Request $request, Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'original_filename' => ['required', 'string', 'max:255'],
        ]);

        $audio->update(['original_filename' => $request->original_filename]);

        return redirect()->route('audios.show', $audio)
            ->with('success', 'Nombre actualizado correctamente.');
    }

    public function attachTag(Request $request, Audio $audio)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'tag_id' => ['required', 'exists:tags,id'],
        ]);

        $audio->tags()->syncWithoutDetaching([$request->tag_id]);

        return redirect()->route('audios.show', $audio)
            ->with('success', 'Etiqueta añadida correctamente.');
    }

    public function detachTag(Audio $audio, \App\Models\Tag $tag)
    {
        if ($audio->user_id !== auth()->id()) {
            abort(403);
        }

        $audio->tags()->detach($tag->id);

        return redirect()->route('audios.show', $audio)
            ->with('success', 'Etiqueta eliminada correctamente.');
    }
}
