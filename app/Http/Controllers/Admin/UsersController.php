<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\PinsImport;
use App\Imports\UsersImport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Types\Object_;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $file_list = [];
        if ($handle = opendir(storage_path('app/public/spreadsheet'))) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $file_list [] =  $file;
                }
            }
            closedir($handle);
        }

        return view('admin.pins.index')->with('file_list',$file_list);
    }

    public function importFile(){
        return view('admin.pins.index');
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|max:99999|mimes:xlsx,ods,xls,csv'
        ]);

        if ($validator->passes()) {
            //storage/app/spreadsheet
            $file = $request->file('file');
            $utime = strtotime('now');
            $ext = $file->getClientOriginalExtension();
            $filename = 'pins' . $utime . '.' . $ext;
            $request->file('file')->storeAs('/public/spreadsheet', $filename);

            DB::table('pin_imports')->truncate();
            Excel::import(new PinsImport, '/public/spreadsheet/' . $filename);

            return redirect()->back()
                ->with(['success' => 'Successfully updated pins with: ' . $filename]);
        } else {
            return redirect()->back()
                ->with(['errors' => $validator->errors()->all()]);
        }
    }

}
