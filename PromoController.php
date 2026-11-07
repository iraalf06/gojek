<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Menampilkan daftar promo
     */
    public function index()
    {
        $promos = Promo::orderBy('id_promo', 'desc')->get();

        return view('promo.index', compact('promos'));
    }

    /**
     * Form tambah promo
     */
    public function create()
    {
        return view('promo.create');
    }

    /**
     * Simpan promo
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_promo' => 'required|unique:promo,kode_promo|max:20',
            'nama_promo' => 'required|max:100',
            'diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required',
        ]);

        Promo::create($request->all());

        return redirect()
            ->route('promo.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    /**
     * Form edit promo
     */
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);

        return view('promo.edit', compact('promo'));
    }

    /**
     * Update promo
     */
    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $request->validate([
            'kode_promo' => 'required|max:20|unique:promo,kode_promo,' . $id . ',id_promo',
            'nama_promo' => 'required|max:100',
            'diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required',
        ]);

        $promo->update($request->all());

        return redirect()
            ->route('promo.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    /**
     * Hapus promo
     */
    public function destroy($id)
    {
        Promo::destroy($id);

        return redirect()
            ->route('promo.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}