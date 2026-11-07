<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;

class AktivitasController extends Controller
{
    public function index()
    {
        $aktivitas = Aktivitas::with([
            'user',
            'pesanan'
        ])
        ->orderBy('tanggal','desc')
        ->get();

        return view('aktivitas.index', compact('aktivitas'));
    }

    public function show($id)
    {
        $aktivitas = Aktivitas::with([
            'user',
            'pesanan'
        ])->findOrFail($id);

        return view('aktivitas.show', compact('aktivitas'));
    }

    public function destroy($id)
    {
        Aktivitas::destroy($id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success','Aktivitas berhasil dihapus.');
    }
}