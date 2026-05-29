<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class AdminController extends Controller
{
    public function index()
    {
        // Uso de almacenamiento
        $audiosPath = Storage::disk('audios')->path('');
        $totalSize  = 0;
        $fileCount  = 0;

        if (is_dir($audiosPath)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($audiosPath)) as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        }

        // Ficheros huérfanos (processed sin audio padre)
        $orphanedFiles = [];
        if (is_dir($audiosPath)) {
            foreach (scandir($audiosPath) as $filename) {
                if ($filename === '.' || $filename === '..') continue;
                if (str_starts_with($filename, 'processed_')) {
                    $originalFilename = str_replace('processed_', '', $filename);
                    if (!Audio::where('stored_filename', $originalFilename)->exists()) {
                        $orphanedFiles[] = $filename;
                    }
                }
            }
        }

        // Estadísticas
        $totalUsers  = User::count();
        $totalAudios = Audio::count();

        return view('admin.index', compact(
            'totalSize',
            'fileCount',
            'orphanedFiles',
            'totalUsers',
            'totalAudios'
        ));
    }

    public function users()
    {
        $users = User::withCount('audios')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users')
                ->with('error', 'No puedes eliminar una cuenta de administrador.');
        }

        // Eliminar ficheros del usuario
        foreach ($user->audios as $audio) {
            Storage::disk('audios')->delete($audio->stored_filename);
            if ($audio->processed_filename) {
                Storage::disk('audios')->delete($audio->processed_filename);
            }
        }

        $user->delete();

        ToastMagic::success('Usuario eliminado correctamente.');
        return redirect()->route('admin.users');
    }

    public function destroyOrphan(string $filename)
    {
        Storage::disk('audios')->delete($filename);

        ToastMagic::success('Fichero huérfano eliminado correctamente.');
        return redirect()->route('admin.index');
    }

    public function purgeProcessed()
    {
        $count = 0;

        DB::transaction(function () use (&$count) {
            $audios = Audio::whereNotNull('processed_filename')->get();

            foreach ($audios as $audio) {
                Storage::disk('audios')->delete($audio->processed_filename);
                $audio->update([
                    'processed_filename' => null,
                    'status'             => 'pending',
                ]);
                $count++;
            }
        });

        ToastMagic::success("Se han purgado {$count} audios procesados correctamente.");
        return redirect()->route('admin.index');
    }
}
