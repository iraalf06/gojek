<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\Promo;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahUser = User::count();
        $jumlahDriver = Driver::count();
        $jumlahPesanan = Pesanan::count();

        $promoAktif = Promo::where('status','Aktif')->count();

        $jumlahPembayaran = Pembayaran::where('status','Lunas')->sum('total');

        $rataRating = Rating::avg('nilai');

        // Grafik Status Pesanan
        $statusPesanan = Pesanan::select(
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('status')
            ->pluck('total','status');

        // Grafik Pendapatan per Bulan
        $pendapatanBulanan = Pembayaran::select(
                DB::raw('MONTH(tanggal_bayar) as bulan'),
                DB::raw('SUM(total) as total')
            )
            ->where('status','Lunas')
            ->groupBy(DB::raw('MONTH(tanggal_bayar)'))
            ->pluck('total','bulan');

        return view('dashboard.index', compact(
            'jumlahUser',
            'jumlahDriver',
            'jumlahPesanan',
            'promoAktif',
            'jumlahPembayaran',
            'rataRating',
            'statusPesanan',
            'pendapatanBulanan'
        ));
    }
}