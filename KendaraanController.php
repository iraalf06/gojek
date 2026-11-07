<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Driver;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * Menampilkan daftar kendaraan
     */
    public function index()
    {
        $kendaraans = Kendaraan::with('driver')
                        ->orderBy('id_kendaraan', 'desc')
                        ->get();

        return view('kendaraan.index', compact('kendaraans'));
    }

    /**
     * Form tambah kendaraan
     */
    public function create()
    {
        $drivers = Driver::orderBy('nama')->get();

        return view('kendaraan.create', compact('drivers'));
    }

    /**
     * Simpan kendaraan
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_driver' => 'required|exists:driver,id_driver',
            'jenis_kendaraan' => 'required',
            'merk' => 'required|max:100',
            'model' => 'required|max:100',
            'nomor_polisi' => 'required|max:20|unique:kendaraan,nomor_polisi',
            'warna' => 'required|max:50',
            'tahun_kendaraan' => 'required|digits:4',
            'is_aktif' => 'required'
        ]);

        Kendaraan::create($request->all());

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Form edit kendaraan
     */
    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $drivers = Driver::orderBy('nama')->get();

        return view('kendaraan.edit', compact('kendaraan', 'drivers'));
    }

    /**
     * Update kendaraan
     */
    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'id_driver' => 'required|exists:driver,id_driver',
            'jenis_kendaraan' => 'required',
            'merk' => 'required|max:100',
            'model' => 'required|max:100',
            'nomor_polisi' => 'required|max:20|unique:kendaraan,nomor_polisi,' . $id . ',id_kendaraan',
            'warna' => 'required|max:50',
            'tahun_kendaraan' => 'required|digits:4',
            'is_aktif' => 'required'
        ]);

        $kendaraan->update($request->all());

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    /**
     * Hapus kendaraan
     */
    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $kendaraan->delete();

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}