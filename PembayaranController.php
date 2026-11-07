<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran
     */
    public function index()
    {
        $pembayarans = Pembayaran::with([
            'pesanan.user',
            'pesanan.driver'
        ])
        ->orderBy('id_pembayaran', 'desc')
        ->get();

        return view('pembayaran.index', compact('pembayarans'));
    }

    /**
     * Tidak digunakan karena pembayaran dibuat otomatis
     */
    public function create()
    {
        return redirect()->route('pembayaran.index');
    }

    /**
     * Tidak digunakan
     */
    public function store(Request $request)
    {
        return redirect()->route('pembayaran.index');
    }

    /**
     * Tidak digunakan
     */
    public function show($id)
    {
        return redirect()->route('pembayaran.index');
    }

    /**
     * Form edit pembayaran
     */
    public function edit($id)
    {
        $pembayaran = Pembayaran::with([
            'pesanan.user',
            'pesanan.driver'
        ])->findOrFail($id);

        return view('pembayaran.edit', compact('pembayaran'));
    }

    /**
     * Update status pembayaran
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        $data = [
            'status' => $request->status
        ];

        if ($request->status == 'Lunas') {
            $data['tanggal_bayar'] = now();
        }

        $pembayaran->update($data);

        return redirect()
            ->route('pembayaran.index')
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Hapus pembayaran
     */
    public function destroy($id)
    {
        Pembayaran::destroy($id);

        return redirect()
            ->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}