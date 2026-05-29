<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class TagController extends Controller
{
    public function index()
    {
        $tags = auth()->user()->tags()->latest()->get();
        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        auth()->user()->tags()->firstOrCreate(
            ['name' => $request->name],
            ['color' => $request->color]
        );

        ToastMagic::success('Etiqueta creada correctamente.');
        return redirect()->route('tags.index');
    }

    public function update(Request $request, Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $tag->update($request->only('name', 'color'));

        ToastMagic::success('Etiqueta actualizada correctamente.');
        return redirect()->route('tags.index');
    }

    public function destroy(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $tag->delete();

        ToastMagic::success('Etiqueta eliminada correctamente.');
        return redirect()->route('tags.index');
    }
}