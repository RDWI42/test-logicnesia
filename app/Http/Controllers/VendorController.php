<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('vendor',[
            'title' => 'Vendor',
            'validasi' => null,
            'DataVendor' => File::orderBy("id")->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'qty' => 'required|min:1|max:50',
        ]);
        if ($validasi->fails()) {
            $data['success'] = 0;
            $data['error'] = $validasi->errors()->first('qty');
        }else{
            $input = $validasi->validated();
            $addfile = [
                'filename' => $request->filename,
                'path' => $request->path,
                'goods_qty' => $input['qty'],
                'upload_date' => date('Y-m-d')
            ];

            $user = File::Create($addfile);
            $data['success'] = 1;
            $data['message'] = 'Data Berhasil Disimpan'; 
            $data['edit'] = 0;
        }
        return response()->json($data);
    }

    public function downloadFile($file)
    {
        $gePath = File::where('id',$file)->first(['path'])->path;
        $split = explode('/',$gePath);
        $file_path = public_path('files/'.$split[4]);    
        return response()->download($file_path);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $File
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $File)
    {
        $validasi = Validator::make($request->all(),[
            'qty' => 'required|min:1|max:50',
        ]);
        if ($validasi->fails()) {
            $data['success'] = 0;
            $data['error'] = $validasi->errors()->first('qty');
        }else{
            $input = $validasi->validated();
            $editfile = [
                'goods_qty' => $input['qty']
            ];
            if($request->path != null){
                $editfile['path'] = $request->path;
                $editfile['upload_date'] = date('Y-m-d');
                $editfile['filename'] = $request->filename;
            }

            $user = File::where('id',$request->id)->update($editfile);
            $data['success'] = 1;
            $data['message'] = 'Data Berhasil Disimpan'; 
            $data['edit'] = 1;
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $File
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        File::destroy($request->id);

        return back()->with('success', 'Delete');
    }

    public function UploadFile(Request $request){
        $data = array();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {

            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file');// Error response

        }else{
            if($request->file('file')) {

                $file = $request->file('file');
                $filename = time().'_'.$file->getClientOriginalName();

                // File extension
                $extension = $file->getClientOriginalExtension();

                // File upload location
                $location = 'files';

                // Upload file
                $file->move($location,$filename);
                
                // File path
                $filepath = url('files/'.$filename);

                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $filepath;
                $data['extension'] = $extension;
            }else{
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.'; 
            }
        }

        return response()->json($data);
    }
}
