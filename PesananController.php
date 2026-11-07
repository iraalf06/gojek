<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Driver;
use App\Models\Layanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan
     */
    public function index()
    {
        $pesanans = Pesanan::with(['user', 'driver', 'layanan'])
            ->orderBy('id_pesanan', 'desc')
            ->get();

        return view('pesanan.index', compact('pesanans'));
    }

    /**
     * Form tambah pesanan
     */
    public function create()
    {
        $users = User::all();
        $drivers = Driver::all();
        $layanans = Layanan::where('status', 'Aktif')->get();

        return view('pesanan.create', compact(
            'users',
            'drivers',
            'layanans'
        ));
    }

    /**
     * Simpan pesanan
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'id_driver' => 'required',
            'id_layanan' => 'required',
            'lokasi_jemput' => 'required',
            'lokasi_tujuan' => 'required',
            'jarak' => 'required|numeric',
            'tarif' => 'required|numeric',
            'status' => 'required',
        ]);

        // Simpan pesanan
        $pesanan = Pesanan::create([
            'id_user' => $request->id_user,
            'id_driver' => $request->id_driver,
            'id_layanan' => $request->id_layanan,
            'id_promo' => null,
            'lokasi_jemput' => $request->lokasi_jemput,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'jarak' => $request->jarak,
            'tarif' => $request->tarif,
            'catatan' => $request->catatan,
            'nama_penerima' => $request->nama_penerima,
            'no_hp_penerima' => $request->no_hp_penerima,
            'berat_paket' => $request->berat_paket,
            'estimasi_waktu' => $request->estimasi_waktu,
            'status' => $request->status,
            'tanggal' => now(),
        ]);

        // Otomatis membuat pembayaran Cash
        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode' => 'Cash',
            'total' => $pesanan->tarif,
            'status' => 'Belum Bayar',
            'tanggal_bayar' => null,
        ]);

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan berhasil ditambahkan.');
    }

    /**
     * Form edit pesanan
     */
    public function edit($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $users = User::all();
        $drivers = Driver::all();
        $layanans = Layanan::all();

        return view('pesanan.edit', compact(
            'pesanan',
            'users',
            'drivers',
            'layanans'
        ));
    }

    /**
     * Update status pesanan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $pesanan = Pesanan::findOrFail($id);

        $pesanan->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan berhasil diperbarui.');
    }

    /**
     * Hapus pesanan
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // Hapus pembayaran yang terkait
        Pembayaran::where('id_pesanan', $pesanan->id_pesanan)->delete();

        // Hapus pesanan
        $pesanan->delete();

        return redirect()
            ->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }
}