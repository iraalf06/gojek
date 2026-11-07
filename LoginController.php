<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $admin = Admin::where('email',$request->email)->first();

        if(!$admin){
            return back()->with('error','Email tidak ditemukan.');
        }

        if(!Hash::check($request->password,$admin->password)){
            return back()->with('error','Password salah.');
        }

        session([
            'admin_id'=>$admin->id_admin,
            'admin_nama'=>$admin->nama
        ]);

        return redirect('/admin/dashboard');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/admin/login');
    }
}