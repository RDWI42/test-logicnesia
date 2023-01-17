<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('login',[
            'title' => 'Login'
        ]);
    }

    public function loginProcess(Request $request){
        $validasi = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        if(Auth::attempt($validasi)){
            $request->session()->regenerate();
            return redirect()->intended('/home');
        }
        return back()->with('LoginError','login Failed');
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/");
    }
}
