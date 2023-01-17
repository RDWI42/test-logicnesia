<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        return view('home',[
            'title' => 'Home',
            'validasi' => null,
            'totalUser' => [
                'Supervisor' => DB::table("users")->where('role','Supervisor')->count(),
                'Admin' => DB::table("users")->where('role','Admin')->count(),
                'Vendor' => DB::table("users")->where('role','Vendor')->count()
            ],
            'totalFile' => DB::table("files")->count(),
        ]);
    }
}
