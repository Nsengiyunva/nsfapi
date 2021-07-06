<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\DocumentFile;
use App\Models\ProcurementItems;
use App\Models\Remark;
use App\User;
use App\Models\Children;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ApplicationController extends Controller
{
    public function saveItems( Request $request){
        if( sizeof( $request->items ) > 0 ){
            foreach( $request->items as $key => $value) {
                $sql = DB::table('applications')->insert(
                    [ 'description' => $value[ 'itemDescription' ], 
                      'subject' => $value[ 'itemSubject' ],
                      'userid' => $value[ 'userid' ],
                      'quantity' => $value[ 'itemQuantity' ],
                      'unit_cost' => $value[ 'itemUnitCost' ],
                      'created_at' => now(),
                      'updated_at' =>  now()
                    ]
                );
           }
           return response()->json([
               "message" => "Procurement items were saved successfully in the database"
           ]);
        }
    }
    public function addSpouse( Request $request ){
        $record  = Children::where('staff_id',  $request->staff_id );
        if( !empty( $record ) ){
           $sql = DB::select('DELETE FROM staff_children WHERE staff_id 
                LIKE "%'.$request->staff_id.'%"  AND spouse_name !=""');
            
        }

        if( sizeof( $request->spouses ) > 0 ){
            foreach( $request->spouses as $spouse ){
                $sql = DB::table('staff_children')->insert(
                    [
                        'staff_id' => $request->staff_id,
                        'spouse_name' => $spouse['spouse_name'],
                        'spouse_phone' => $spouse['spouse_phone'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                        'type' => 'spouse'
                    ]
                );
            }
            return response()->json([
                "message" => "Spouses have been Added"
            ]);
        }
    }
    public function addChildren( Request $request ) {
        $record  = Children::where('staff_id',  $request->staff_id );
        if( !empty( $record ) ){
           $sql = DB::select('DELETE FROM staff_children WHERE staff_id 
                LIKE "%'.$request->staff_id.'%" AND child_name !="" ');
            
        }

        if( sizeof( $request->children ) > 0 ){
            foreach( $request->children as $child ){
                $sql = DB::table('staff_children')->insert(
                    [
                        'staff_id' => $request->staff_id,
                        'child_name' => $child['child_name'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                        'type' => 'child'
                    ]
                );
            }
            return response()->json([
                "message" => "Spouse Children have been Added"
            ]);
        }
    }
    public function getChildren(Request $request){
        $record = DB::select("SELECT DISTINCT * FROM staff_children 
            WHERE staff_id LIKE  '".$request->staff_id."' ");
        // $records = Children::where('staff_id', $request->staff_id )->get();
        if( !empty( $record ) ){
            return response()->json( $record  );
        }
    }

    public function store(Request $request) {
        //capture all input field names
        $requistion = new Application;
        $requistion->entity_type = $request->entity_type;
        $requistion->specify_other_type = $request->specify_other_type;
        $requistion->name = $request->name;

        $requistion->signed = $request->signed;
        $requistion->compliant = $request->compliant;
        $requistion->completed = $request->completed;
        $requistion->composition = $request->composition;
        $requistion->calendar = $request->calendar;
        $requistion->payment = $request->payment;
        $requistion->any_other = $request->any_other;

        $requistion->save();
        if( $requistion->id ){
            return response()->json([
                'success' => true,
                'data' => $requistion
            ]);
        }return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }
    public function fetchAll(){
       $records =  Requistion::all();
       return response()->json( $records );
    }
    public function getAll(Request $request){
        if($request->role == "MD"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%MD%' ");
        }
        if($request->role == "GM_FINANCE"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%GM_FINANCE%' 
            OR status LIKE '%GM_AUTHORIZE_VOUCHER%' OR status LIKE '%GM_FINANCE_AUTHORIZE%' 
            OR status LIKE '%PENDING_GM_AUTHORIZATION%' OR status LIKE '%PENDING_GM_FINANCE_TO_PAY%' ");
        }
        if($request->role == "CIA"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%CIA%' ");
        }
        if($request->role == "HOD"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE department LIKE '%".$request->department."%' ");
        }
        if($request->role === "CASHIER"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%CASHIER%' 
                OR status LIKE '%PROCESS_PAYMENT%' OR status LIKE '%PAYING%'   OR status LIKE '%PROCESSING_PAYMENT%'
                OR status LIKE '%PAID%'  ");
        }
        if($request->role === "PROCUREMENT"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%PENDING_PROCUREMENT%'");
        }
        if($request->role === "User"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE email_address LIKE '%".$request->email_address."%' ");
        }
        if($request->role === "All"){
            $records = DB::select("SELECT DISTINCT * FROM payment_requistion");
        }
       
        if( !empty( $records ) ){
            return response(
                [ 
                    'success' => true,
                    'results' => $records
                ], 200 );
        }
        return response([ 
            'success' => false, 
            'message' => 'There are no records available on the server' 
        ]);
    }
    public function getHOD(Request $request){
        $records = DB::select("SELECT DISTINCT * FROM payment_requistion 
        WHERE department LIKE '%".$request->department."%' AND 
        status LIKE '%PENDING_HOD%' ");

        return response()->json( $records );
    }
    public function updateItem(Request $request){
        $record = Requistion::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->belongsTo = $request->input('belongsTo');
        $record->feature = !empty( $request->input('feature') ) ?  $request->input('feature') : 'requisition';
        $record->director_approved = $request->input('director_approved');
        $record->cashier_approved = $request->input('cashier_approved');
        $record->voucher_approved = $request->input('voucher_approved');
        $record->cashier_date_approved = $request->input('cashier_date_approved');
        $record->director_date_approved = $request->input('director_date_approved');
        $record->voucher_date_approved = $request->input('voucher_date_approved');
        
        $record->save();

        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function updateCashierVoucher(Request $request){
        $record = Requistion::where('id', $request->id )->first();
        $record->status = $request->input('status');
        $record->belongsTo = $request->input('belongsTo');
        $record->feature = !empty( $request->input('feature') ) ?  $request->input('feature') : 'requisition';
        $record->payee = $request->input('payee');
        $record->cashier_amount = $request->input('cashier_amount');
        $record->cashier_description = $request->input('cashier_description');
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function getPaymentVoucher(Request $request){
        $records =Requistion::where('id', $request->id )->first();
        return response()->json( $records );
    }
    public function updateRequisition(Request $request){
        $record = Requistion::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->department = $request->input('department');
        $record->subject = $request->input('subject');
        $record->description = $request->input('description');
        $record->revert = $request->input('revert');
        $record->email_address = $request->input('email_address');
        $record->feature = 'requisition';
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function approved(){
        $record = DB::select("SELECT * FROM payment_requistion WHERE status = 'PAID' OR status = 'CONTRACT_AWARDED' ");
        return response()->json([
            "success" => true,
            "data" => $record
        ]);
    }
    public function getProcurementItems(Request $request){
        $record = DB::table( 'procurement_items' )->where('userid', $request->staff_id )->get();
        return response()->json( $record );
    }
    public function getRole(Request $request){
        $record = DB::table('users')->where( 'email_address', $request->email )->get();
        return response()->json( $record );
    }
    public function getRemarks(Request $request){
        // WHERE requisition_id = ".$request->id." AND destination = ".$request-> ." ORDER BY created_at asc
        $records = DB::select("SELECT DISTINCT * FROM user_remarks WHERE requisition_id = ".$request->id." AND destination='".$request->destination."'");
        return response()->json( $records );
    }
    public function add_procure_document( Request $request ) {
        $document = new DocumentFile;
        $document->file = $request->file;
        $document->name = $request->title;
        $document->size = $request->size;
        $document->type = $request->type;
        $document->description = $request->details;
        $document->requisition_id = $request->requisition_id;
        $document->details = $request->details;
        // $document->status = $request->status;
        $document->created_at = now();
        $document->updated_at = now();

        $document->save();
        // if( $document->id ){
        //     return response()->json([
        //         'result' => $document
        //     ]);
        // }
    }
    public function add_remark(Request $request){
        $remark = new Remark;
        $remark->user_id = $request->user_id;
        $remark->requisition_id = $request->request_id;
        $remark->date = $request->date;
        $remark->remarks = $request->remarks; 
        $remark->remarks_by = $request->remarks_by;
        $remark->organisation = $request->organisation;
        $remark->destination = $request->destination;
        $remark->type = $request->type;
        $remark->email_address = $request->email_address;
        $remark->created_at = now();
        $remark->updated_at = now();

        $remark->save();
        if( $remark->id > 0 ){
            return response()->json([
                'message' => 'Remark has been saved successfully'
            ]);
        }

    }
    public function getOfficerRemarks(Request $request){
        $results = DB::select( "SELECT * FROM user_remarks 
            WHERE destination = 'User' 
            AND email_address = ".$request->id."" );
        return response()->json( $results );
    }
    public function getUserByEmail( Request $request ) {
        $record = DB::select( "SELECT DISTINCT * FROM users WHERE email_address = '".$request->email_address."'" );
        if( !empty( $record ) ){
            return response()->json( $record );
        }
        else {
            return response()->json([]);
        }
    }
    public function generateFile(Request $request) {
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();

        $html ='<body style="display: flex; background-color: #FFFDD0;">
            <div style="height:100%; width: 100%;">
                <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
                <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
                    style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
                </div>

                <div>
                    <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
                    <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
                <div>
                <div>
                    <p style="line-height: 8mm;padding: 2mm;">
                        <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
                        <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
                        <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
                        <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
                        Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
                        any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
                        (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
                        <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
                        <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
                        is hereby granted license by the National Foresty Authority( Licensor ) to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
                        in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
                        <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
                        This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
                        from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
                        in the license agreement per hectare for purposes of growing trees.
                        
                        <div style="padding-bottom: 2mm;">
                        <h4>APPROVED BY THE DIRECTOR:</h4>
                        Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
                        </div>

                        <div style="padding-top: 2mm;">
                        <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
                        Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
                        </div>
                    </p>
                        
                    <p>
                        <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
                    </p>
                    <p style="padding-bottom: 2mm;">
                        <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
                        <div>Notes:</div>
                    </p>

                </div>

                </div>
            </div>
        </body>';
        $mpdf->setTitle("National Forestry Authority License Document");
        $mpdf->SetWatermarkImage( "'.$root.'/dms_backend/assets/logo.png'" );
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode('fullpage');
        $mpdf->WriteHTML( $html );
        $mpdf->Output('output.pdf','F');

        $path = public_path('output.pdf');
        $data = file_get_contents($path);

        $base64 = base64_encode($data);
        return response()->json( [ "file" => $base64 ] );
        // return response()->download( $file, 'filename.pdf');

    }
    public function fetchApprovedRequests(Request $request) {
        if( $request->type == "director"){
            $records = Requistion::where('director_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }
        if( $request->type == "cashier"){
            $records = Requistion::where('cashier_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }

        if( $request->type == "signed"){
            $records = Requistion::where('voucher_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }
        
    }
    public function updateUser(Request $request){
        $sql = "UPDATE users 
            SET role = '".$request->role."',
            position = '".$request->position."',
            department = '".$request->department."',
            department_no = '".$request->department_no."',
            next_of_kin = '".$request->next_of_kin."',
            nok_phone = '".$request->nok_phone."',
            nssf_no = '".$request->nssf_no."'
        WHERE email_address = '".$request->email_address."'";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => "User Updated"
        ]);
    }
    public function updatePassword(Request $request){
        $encrypted = bcrypt($request->password);
        $sql = "UPDATE users 
            SET
            password = '".$encrypted."',
            passcode = '".$request->password."'
        WHERE email_address = '".$request->email_address."'";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => $encrypted
        ]);
    }
    public function createDocument(Request $request) {
        // $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        // $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        // require_once $root. '/dms_backend/vendor/autoload.php';
        // $file =  $root.'/dms_backend/documents/file1.pdf';

        // $mpdf = new \Mpdf\Mpdf();

        // $html ='<body style="display: flex; background-color: #ffe087;">
        //     <div style="height:100%; width: 100%;">
        //        <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
        //         <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
        //             style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
        //        </div>

        //         <div>
        //             <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
        //             <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
        //         <div>
        //         <div>
        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
        //                 <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
        //                 <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
        //                 <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
        //                 Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
        //                 any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
        //                 (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
        //                 <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
        //                 <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
        //                 is hereby granted license by the National Foresty Authority( Licensor ) to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
        //                 in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
        //                 <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
        //                 This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
        //                 from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
        //                 in the license agreement per hectare for purposes of growing trees.
                        
        //                 <div style="padding-bottom: 2mm;">
        //                 <h4>APPROVED BY THE DIRECTOR:</h4>
        //                 Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
        //                 </div>

        //                 <div style="padding-top: 2mm;">
        //                 <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
        //                 Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
        //                 </div>
        //             </p>
                       
        //             <p>
        //                 <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
        //             </p>
        //             <p style="padding-bottom: 2mm;">
        //                 <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
        //                 <div>Notes:</div>
        //             </p>

        //         </div>

        //        </div>
        //     </div>
        // </body>';
        // $mpdf->setTitle("List of Approved Requisitions");
        // $mpdf->showWatermarkImage = false;
        // $mpdf->setDisplayMode('fullpage');
        // $mpdf->WriteHTML( $html );
        // $mpdf->Output('output.pdf','F');

        // $path = public_path('output.pdf');
        // $data = file_get_contents($path);

        // $base64 = base64_encode($data);
        // // return response()->json( [ "file" => $base64 ] );
        // return response()->download( $file, 'filename.pdf');

    }

    public function generateDDALicense( Request $request ){
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";

        $html = '
            <html>
                <head>
                    <style>
                        .container {
                            height: 100%;
                            width: 100%;
                        }
                        .image-container {
                            display: flex;
                            justify-items: center;
                            align-items: center;
                            width: 100%;
                        }
                        .logo {
                            width: 120;
                            padding-left: 65mm;
                        }
                        .centered {
                            text-align: center;
                        }
                        .subtitle {
                            font-size: 14px;
                        }
                        span.first {
                            padding-left: 0;
                            padding-right: 25mm;
                        }
                        span.last {
                            padding-left: 25mm;
                            padding-right: 0;
                        }
                        .horizontal_dotted_line:after {
                            content: "................................";
                            padding: 5px;
                        }
                        .company_name_text {
                            border-bottom: 2px dotted;
                            padding: 10px;
                            width: 100%;
                        }
                        .label-title {
                            padding-top: 1em;
                        }
                    </style>
                </head>
            <body>
                <div class="container">
                    <div class="image-container">
                        <img class="logo" src="assets/dda_logo.png" />&nbsp;
                    </div>
                    <div>
                        <h3 class="centered title">DAIRY DEVELOPMENT AUTHORITY</h3>
                        <h5 class="centered">ESTABLISHED BY ACT OF PARLIAMENT - THE DAIRY INDUSTRY ACT, 1998 </h5>
                        <p class="centered subtitle">The Dairy ( Marketing and Processing of milk and milk Products ) Regulations, 2003 and as<br/> amended 2006</p>
                        <h5 class="centered">CERTIFICATE OF REGISTRATION TO OPERATE COOLERS</h5>

                        <div class="flexed centered">
                            <span class="first">FIFTH SCHEDULE</span>
                            <span class="last">REG. 10( 4 )</span>
                        </div>

                        <div class="flexed centered">
                            <span class="first">FIFTH SCHEDULE</span>
                            <span class="last">REG. 10( 4 )</span>
                        </div>
                        <div>
                            <p>This certificate is hereby issued to: <br/></p>
                            
                            <div>
                                <div class="label-title">Name of Company: <span class="company_name_text"></span></div>
                                <div class="label-title">Address: <span class="company_name_text"></span></text>
                                <div class="label-title">Valid from 1st January 2021 to the 31st December 2022</div>
                                <div>Amount ..................<div>
                                <div>Receipt No ..............</div>
                                <div>
                                    <p>This certificate is issued on the following conditions:</p>
                                    <ol>
                                        <li> The premise and surrondings should be hygienically kept </li>
                                        <li> The personnel should be periodically examined and be issued with medical certificates for health fitness</li>
                                        <li> Only fresh and wholesome raw milk should be sold </li>
                                        <li> No other goods and materials should be kept or sold in the Dairy </li>
                                    </ol>
                                </div>

                                <div>
                                    <span>....................</span>
                                    <h6>EXECUTIVE DIRECTOR</h6>
                                    <p>Dairy Development Authority</p>
                                </div>

                                <div>
                                    <span>....................</span>
                                    <h6>DATE</h6>
                                </div>

                                <b>P.O.Box 34006, Tel: 256-41-343901/3, Kampala. E-mail: ed@dda.or.ug; website: https://www.dda.go.ug</b>
                            </div>
                        </div>
                    </div>
                </div>
            </body>';

            
            require_once $root. '/dms_backend/vendor/autoload.php';

            $mpdf = new \Mpdf\Mpdf( [
                'margin_left' => 20,
                'margin_right' => 15,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'margin_header' => 10,
                'margin_footer' => 10
            ] );

            $mpdf->SetProtection( array( 'print' ) );
            $mpdf->SetTitle( 'DAIRY DEVELOPMENT AUTHORITY' );
            $mpdf->SetAuthor("NITA-U");
            $mpdf->watermark_font = 'DejaVuSansCondensed';

            $mpdf->SetDisplayMode( 'fullpage' );
            $mpdf->SetWatermarkImage('assets/dda_logo.png');
            $mpdf->showWatermarkImage = true;
            $mpdf->watermarkTextAlpha = 0.5;

            $mpdf->WriteHTML( $html );
            // $mpdf->Output();
            $mpdf->Output('dda.pdf','F');

            $path = public_path('dda.pdf');
            $data = file_get_contents($path);

            $base64 = base64_encode($data);
            return response()->json( [ "file" => $base64 ] );
    }


    public function downloadDocument(Request $request) {
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML('<div>Section 1 text</div>');

        $mpdf->Output('output.pdf','F');

        $path = public_path( $file );
        $data = file_get_contents($path);

        $base64 = base64_encode($data);

        $html ='<body style="display: flex; background-color: #ffe087;">
            <div style="height:100%; width: 100%;">
                <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
                <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
                    style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
                </div>

                <div>
                    <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
                    <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
                <div>
                <div>
                    <p style="line-height: 8mm;padding: 2mm;">
                        <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
                        <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
                        <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
                        <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
                        Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
                        any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
                        (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
                        <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
                        <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
                        is hereby granted license by the National Foresty Authority( Licensor ) to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
                        in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
                        <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
                        This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
                        from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
                        in the license agreement per hectare for purposes of growing trees.
                        
                        <div style="padding-bottom: 2mm;">
                        <h4>APPROVED BY THE DIRECTOR:</h4>
                        Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
                        </div>

                        <div style="padding-top: 2mm;">
                        <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
                        Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
                        </div>
                    </p>
                        
                    <p>
                        <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
                    </p>
                    <p style="padding-bottom: 2mm;">
                        <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
                        <div>Notes:</div>
                    </p>

                </div>

                </div>
            </div>
        </body>';
        $mpdf->setTitle("List of Approved Requisitions");
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode('fullpage');
        $mpdf->WriteHTML( $html );
        $mpdf->Output('output.pdf','F');

        $path = public_path('output.pdf');
        $data = file_get_contents($path);

        $base64 = base64_encode($data);
        return response()->json( [ "file" => $base64 ] );
        return response()->download( $file, 'filename.pdf');

    }

    public function country_list(){
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();

        $mpdf->WriteHTML('<div>Section 1 text</div>');

        $mpdf->Output('output.pdf','F');
    }

    public function test() {
        return response()->json([
            "test" => "Test"
        ]);
    }

}
