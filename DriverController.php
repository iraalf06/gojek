<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * Menampilkan daftar driver
     */
    public function index()
    {
        $drivers = Driver::orderBy('id_driver', 'desc')->get();

        return view('driver.index', compact('drivers'));
    }

    /**
     * Menampilkan form tambah driver
     */
    public function create()
    {
        return view('driver.create');
    }

    /**
     * Menyimpan driver baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'email' => 'required|email|unique:driver,email',
            'password' => 'required|min:6',
            'no_hp' => 'required|max:20',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $foto = null;

        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil')->store('driver', 'public');
        }

        Driver::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'foto_profil' => $foto,
            'status' => 'Offline',
            'status_verifikasi' => 'Pending',
            'rating' => 0,
        ]);

        return redirect()
            ->route('driver.index')
            ->with('success', 'Driver berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit driver
     */
    public function edit($id)
    {
        $driver = Driver::findOrFail($id);

        return view('driver.edit', compact('driver'));
    }

    /**
     * Mengupdate data driver
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $request->validate([
            'nama' => 'required|max:100',
            'email' => 'required|email|unique:driver,email,' . $id . ',id_driver',
            'no_hp' => 'required|max:20',
            'status' => 'required',
            'status_verifikasi' => 'required',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $foto = $driver->foto_profil;

        if ($request->hasFile('foto_profil')) {

            // Hapus foto lama
            if ($driver->foto_profil && Storage::disk('public')->exists($driver->foto_profil)) {
                Storage::disk('public')->delete($driver->foto_profil);
            }

            // Upload foto baru
            $foto = $request->file('foto_profil')->store('driver', 'public');
        }

        $driver->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'foto_profil' => $foto,
            'status' => $request->status,
            'status_verifikasi' => $request->status_verifikasi,
        ]);

        return redirect()
            ->route('driver.index')
            ->with('success', 'Driver berhasil diperbarui.');
    }

    /**
     * Menghapus driver
     */
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);

        if ($driver->foto_profil && Storage::disk('public')->exists($driver->foto_profil)) {
            Storage::disk('public')->delete($driver->foto_profil);
        }

        $driver->delete();

        return redirect()
            ->route('driver.index')
            ->with('success', 'Driver berhasil dihapus.');
    }
}