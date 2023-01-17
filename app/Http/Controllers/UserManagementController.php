<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('userManagement',[
            'title' => 'User Management',
            'validasi' => null,
            'DataUser' => User::orderBy("id")->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // $params = $request->except('_method', '_token');
        // dd($params);
        $validasi = Validator::make($request->all(),[
            'username' => 'required|min:3|max:50|unique:users',
            'email' => 'required|email:dns|unique:users',
            'role' => 'required',
            'password' => 'required|min:5|max:255',
        ]);
        if ($validasi->fails()) {
            return redirect('userManagement')->with('status','tambah')
                        ->withErrors($validasi)
                        ->withInput();
        }
        $input = $validasi->validated();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return back()->with('success', 'Create');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if($request->cekPass == 'false'){
            $validasi = Validator::make($request->all(),[
                'username' => 'required|min:3|max:50|unique:users',
                'email' => 'required|email:dns|unique:users',
                'role' => 'required',
            ]);
            if ($validasi->fails()) {
                return response()->json($validasi->errors(), 422);
            }
        }else{
            $validasi = Validator::make($request->all(),[
                'username' => 'required|min:3|max:50|unique:users,username,'.$request->id,
                'email' => 'required|email:dns|unique:users,email,'.$request->id,
                'role' => 'required',
                'password1' => 'required',
                'password2' => 'required'
            ],[
                'password1.required' => 'Password Lama is required',
                'password2.required' => 'Password Baru is required',
            ]);
            if ($validasi->fails()) {
                return response()->json($validasi->errors(), 422);
            }
            $cekPasslama = DB::table('users')->where('id',$request->id)->first(['password'])->password;
            if(!Hash::check($request->password1, $cekPasslama)){
                return response()->json([
                    'success' => false,
                    'message' => 'Password Lama Not Match!',
                    'data'    => $cekPasslama  
                ]);
            }
        }
        $input = $validasi->validated();
        $updateData = [
            'username' => $input['username'],
            'email' => $input['email'],
            'role' => $input['role'],
        ];
        if($request->cekPass == 'true'){
            $updateData['password'] = bcrypt($input['password2']);
        }
        $user = User::where('id',$request->id)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil',
            'data'    => $updateData  
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        User::destroy($request->id);

        return back()->with('success', 'Delete');
    }
}
