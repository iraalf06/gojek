<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with('pesanan')
            ->orderBy('waktu_kirim','desc')
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function show($id)
    {
        $chat = Chat::findOrFail($id);

        return view('chat.show', compact('chat'));
    }

    public function destroy($id)
    {
        Chat::destroy($id);

        return redirect()
            ->route('chat.index')
            ->with('success','Chat berhasil dihapus.');
    }
}