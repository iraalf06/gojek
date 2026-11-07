<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Layanan::orderBy('id_layanan', 'desc')->get();

        return view('layanan.index', compact('layanans'));
    }

    public function create()
    {
        return view('layanan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|max:100',
            'tarif_per_km' => 'required|numeric',
            'deskripsi' => 'required',
            'status' => 'required'
        ]);

        Layanan::create($request->all());

        return redirect()
            ->route('layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $layanan = Layanan::findOrFail($id);

        return view('layanan.edit', compact('layanan'));
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $request->validate([
            'nama_layanan' => 'required|max:100',
            'tarif_per_km' => 'required|numeric',
            'deskripsi' => 'required',
            'status' => 'required'
        ]);

        $layanan->update($request->all());

        return redirect()
            ->route('layanan.index')
            ->with('success', 'Layanan berhasil diupdate.');
    }

    public function destroy($id)
    {
        Layanan::destroy($id);

        return redirect()
            ->route('layanan.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}