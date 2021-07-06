<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\DocumentFile;
use App\Models\Requistion;
use App\License;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Url;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class DocumentFileController extends Controller
{
    public static function store(Request $request){
        $document = new DocumentFile;
         if( sizeof( $request->documents ) > 0 ){
            foreach( $request->documents as $key => $value){
                $sql = DB::table('documents')->insert(
                    [ 'type' => $value[ 'type' ], 
                      'details' => $value[ 'details' ],
                      'name' => $value[ 'title' ],
                      'size' => $value[ 'size' ],
                      'userid' => $value[ 'userid' ],
                      'file' => $value[ 'file' ],
                      'requisition_id' => $value[ 'id' ],
                      'created_at' => now(),
                      'updated_at' =>  now()
                    ]
                );
           }
            return response()->json([
               "message" => "All the files have been saved"
             ], 201);
        } else {
            return response()->json([
                "message" => "No files were attached."
            ]);
        }
    }
    public function updateProcurementRequest(Request $request){
        $record = Requistion::where('id', $request->id )->first();
        $record->procure_added = $request->procure_added;
       
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function fetchUserFiles(Request $request){
        $results = DB::select( "SELECT * FROM documents 
        WHERE requisition_id = ".$request->id."" );
        return response()->json( $results );
    }
    public function add_payroll( Request $request ) {
        $document = new DocumentFile;
        $document->file = $request->file;
        $document->name = $request->title;
        $document->size = $request->size;
        $document->type = $request->type;
        $document->description = $request->details;
        $document->details = $request->details;
        $document->status = $request->status;
        $document->created_at = now();
        $document->updated_at = now();

        $document->save();
        if( $document->id ){
            return response()->json([
                'result' => $document
            ]);
        }
    }
    public function get_all_payrolls(){
        $record = DB::select("SELECT * FROM documents WHERE details LIKE '%payroll%'");
        return response()->json([
            "success" => true,
            "data" => $record
        ]);
    }
    public function updateRecord(Request $request){
        $record = DocumentFile::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
}
