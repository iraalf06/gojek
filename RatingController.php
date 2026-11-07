<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::with([
            'user',
            'driver',
            'pesanan'
        ])
        ->orderBy('id_rating','desc')
        ->get();

        return view('rating.index', compact('ratings'));
    }

    public function show($id)
    {
        $rating = Rating::with([
            'user',
            'driver',
            'pesanan'
        ])->findOrFail($id);

        return view('rating.show', compact('rating'));
    }

    public function destroy($id)
    {
        Rating::destroy($id);

        return redirect()
            ->route('rating.index')
            ->with('success','Rating berhasil dihapus.');
    }
}