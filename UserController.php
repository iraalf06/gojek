<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index()
    {
        $users = User::orderBy('id_user', 'desc')->get();

        return view('user.index', compact('users'));
    }

    /**
     * Menampilkan form tambah user
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Menyimpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'no_hp'        => 'required|max:20',
            'alamat'       => 'required',
            'foto_profil'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $foto = null;

        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil')->store('user', 'public');
        }

        User::create([
            'nama'         => $request->nama,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'no_hp'        => $request->no_hp,
            'alamat'       => $request->alamat,
            'foto_profil'  => $foto,
            'created_at'   => now(),
        ]);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('user.edit', compact('user'));
    }

    /**
     * Mengupdate user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'         => 'required|max:100',
            'email'        => 'required|email|unique:users,email,' . $id . ',id_user',
            'no_hp'        => 'required|max:20',
            'alamat'       => 'required',
            'foto_profil'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $foto = $user->foto_profil;

        if ($request->hasFile('foto_profil')) {

            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $foto = $request->file('foto_profil')->store('user', 'public');
        }

        $user->update([
            'nama'         => $request->nama,
            'email'        => $request->email,
            'no_hp'        => $request->no_hp,
            'alamat'       => $request->alamat,
            'foto_profil'  => $foto,
        ]);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}