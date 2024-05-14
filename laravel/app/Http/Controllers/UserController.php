<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class UserController extends Controller
{
    public function register(){
        return view('register');
    }
    
    public function proses(Request $request){
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'repass' => 'required|same:password', // Menambahkan validasi untuk confirm password
        ], [
            'repass.same' => 'Password confirmation does not match password.', // Menampilkan pesan error khusus
        ]);
    
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role_id'=>1
        ]);
        Session::flash('success', 'Akun berhasil dibuat. Silakan login.');

        return redirect('login');
    }
    
    public function login(){
        $successMessage = Session::get('success');
        return view('login', compact('successMessage'));
    }
    
    
    public function prosesL(Request $request){
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (Auth::user()->role->name == 'admin') {
                return redirect()->route('game.index');
            } else {
                return redirect()->route('landing.index');
            }
        }
    
        return redirect()->back()->with('error', 'Email atau password salah.');
    }

}
