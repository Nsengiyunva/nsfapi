<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function store( Request $request ) {
    //    $record = DB::table('applications')->insert( [
    //         'entity_type' => $request->entity_type,
    //         'specify_other_type' => $request->specify_other_type,
    //         'name' => $request->name,
    //         'signed' => $request->signed,
    //         'compliant' => $request->compliant,
    //         'completed' => $request->completed,
    //         'composition' => $request->composition,
    //         'calendar' => $request->calendar,
    //         'payment' => $request->payment,
    //         'any_other' => $request->any_other 
    //     ] );

    //     if( $record ) {
    //         return response()->json( [
    //             "success" => true,
    //             "result" => "The record has been added successfully to the database"
    //         ] );
    //     } else {
            return response()->json( [
                "success" => false,
                "name" => $request->name,
                "result" => "The record insertion failed"
            ] );
        // }

        
    }    

    public function fetch(){
        return response()->json( [
            "id" => 2
        ] );
    }

}
