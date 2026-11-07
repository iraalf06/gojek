<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenDriver;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenDriverController extends Controller
{
    /**
     * Menampilkan semua dokumen driver
     */
    public function index()
    {
        $dokumens = DokumenDriver::with('driver')
            ->orderBy('id_dokumen', 'desc')
            ->get();

        return view('dokumen_driver.index', compact('dokumens'));
    }

    /**
     * Form tambah dokumen
     */
    public function create()
    {
        $drivers = Driver::all();

        return view('dokumen_driver.create', compact('drivers'));
    }

    /**
     * Simpan dokumen
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_driver' => 'required',
            'jenis_dokumen' => 'required',
            'file_path' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status_verifikasi' => 'required',
        ]);

        $file = $request->file('file_path')
            ->store('dokumen-driver', 'public');

        DokumenDriver::create([
            'id_driver' => $request->id_driver,
            'jenis_dokumen' => $request->jenis_dokumen,
            'file_path' => $file,
            'status_verifikasi' => $request->status_verifikasi,
        ]);

        // Cek status driver setelah tambah dokumen
        $this->updateStatusDriver($request->id_driver);

        return redirect()
            ->route('dokumen-driver.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    /**
     * Form edit dokumen
     */
    public function edit($id)
    {
        $dokumen = DokumenDriver::findOrFail($id);
        $drivers = Driver::all();

        return view('dokumen_driver.edit', compact('dokumen', 'drivers'));
    }

    /**
     * Update dokumen
     */
    public function update(Request $request, $id)
    {
        $dokumen = DokumenDriver::findOrFail($id);

        $request->validate([
            'id_driver' => 'required',
            'jenis_dokumen' => 'required',
            'status_verifikasi' => 'required',
        ]);

        $data = [
            'id_driver' => $request->id_driver,
            'jenis_dokumen' => $request->jenis_dokumen,
            'status_verifikasi' => $request->status_verifikasi,
        ];

        if ($request->hasFile('file_path')) {

            if ($dokumen->file_path) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $data['file_path'] = $request->file('file_path')
                ->store('dokumen-driver', 'public');
        }

        $dokumen->update($data);

        // Update status driver
        $this->updateStatusDriver($request->id_driver);

        return redirect()
            ->route('dokumen-driver.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Hapus dokumen
     */
    public function destroy($id)
    {
        $dokumen = DokumenDriver::findOrFail($id);

        $idDriver = $dokumen->id_driver;

        if ($dokumen->file_path) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        // Update status driver
        $this->updateStatusDriver($idDriver);

        return redirect()
            ->route('dokumen-driver.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Update status verifikasi driver
     */
    private function updateStatusDriver($idDriver)
    {
        $driver = Driver::find($idDriver);

        if (!$driver) {
            return;
        }

        $totalDokumen = DokumenDriver::where('id_driver', $idDriver)->count();

        $dokumenDisetujui = DokumenDriver::where('id_driver', $idDriver)
            ->where('status_verifikasi', 'Disetujui')
            ->count();

        // Driver dianggap lolos jika minimal memiliki 3 dokumen
        // dan semuanya sudah disetujui
        if ($totalDokumen >= 3 && $totalDokumen == $dokumenDisetujui) {

            $driver->status_verifikasi = 'Disetujui';

        } else {

            $driver->status_verifikasi = 'Pending';

        }

        $driver->save();
    }
}