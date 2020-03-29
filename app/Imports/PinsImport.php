<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\PinImports;

class PinsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return PinImports|null
     */
    public function model(array $row)
    {
        return new PinImports([
            'title' =>$row[2],
            'category' =>$row[4],
            'privacy' =>$row[5],
            'latitude' =>$row[7],
            'longitude'=>$row[8],
            'address'=>$row[9],
            'city'=>$row[10],
            'country'=>$row[11],
        ]);
    }
}
