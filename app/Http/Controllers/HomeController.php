<?php

namespace App\Http\Controllers;

use App\Imports\PinsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pins = DB::select('select * from pin_imports');
        $arr = [];
        foreach ($pins as $row){
            $arr[]=(array)$row;
        }

        return view('home')->with('pins',$arr);
    }
}

