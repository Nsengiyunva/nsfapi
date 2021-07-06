<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quaterly;
use App\Models\Activity;
use App\Models\Swimmer;
use App\Models\WaterPolo;
use App\Models\Master;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuaterController extends Controller
{
    public function store(Request $request) {
        $quater = new Quaterly;
        $quater->usf_member_name = $request->usf_member_name;
        $quater->name_chairman = $request->name;
        $quater->phone_contact = $request->phone_contact;
        $quater->email_address = $request->email_address;
        
        $quater->save();

        if( $quater->id ){
            return response()->json([
                'success' => true,
                'data' => $quater
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_activity(Request $request) {
        $activity = new Activity;
        $activity->quater_id = $request->quater_id;
        $activity->description = $request->description;
        $activity->date = $request->date;
        $activity->objective = $request->objective;
        $activity->tasks = $request->tasks;
        $activity->internal_participants = $request->internal_participants;
        $activity->external_participants = $request->external_participants;
        $activity->remarks = $request->remarks;
        
        $activity->save();

        if( $activity->id ){
            return response()->json([
                'success' => true,
                'data' => $activity
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_swimmer(Request $request) {
        $swimmer = new Swimmer;
        $swimmer->quater_id = $request->quater_id;
        $swimmer->junior_females = $request->junior_females;
        $swimmer->junior_males = $request->junior_males;
        $swimmer->senior_females = $request->senior_females;
        $swimmer->senior_males = $request->senior_males;
        $swimmer->comments = $request->comments;
        
        $swimmer->save();

        if( $swimmer->id ){
            return response()->json([
                'success' => true,
                'data' => $swimmer
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_waterpolo(Request $request) {
        $polo = new WaterPolo;
        $polo->quater_id = $request->quater_id;
        $polo->junior_females = $request->junior_females;
        $polo->junior_males = $request->junior_males;
        $polo->senior_females = $request->senior_females;
        $polo->senior_males = $request->senior_males;
        $polo->comments = $request->comments;
        
        $polo->save();

        if( $polo->id ){
            return response()->json([
                'success' => true,
                'data' => $polo
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_master(Request $request) {
        $master = new Master;
        $master->quater_id = $request->quater_id;
        $master->junior_females = $request->junior_females;
        $master->junior_males = $request->junior_males;
        $master->senior_females = $request->senior_females;
        $master->senior_males = $request->senior_males;
        $master->comments = $request->comments;
        
        $master->save();

        if( $master->id ){
            return response()->json([
                'success' => true,
                'data' => $master
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_coach(Request $request) {
        $coach = new Coach;
        $coach->quater_id = $request->quater_id;
        $coach->junior_females = $request->junior_females;
        $coach->junior_males = $request->junior_males;
        $coach->senior_females = $request->senior_females;
        $coach->senior_males = $request->senior_males;
        $coach->comments = $request->comments;
        
        $coach->save();

        if( $coach->id ){
            return response()->json([
                'success' => true,
                'data' => $coach
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_administrator(Request $request) {
        $administrator = new Administrator;
        $administrator->quater_id = $request->quater_id;
        $administrator->first_name = $request->first_name;
        $administrator->last_name = $request->last_name;
        $administrator->gender = $request->gender;
        $administrator->position = $request->position;
        $administrator->status = $request->status;
        
        $administrator->save();

        if( $administrator->id ){
            return response()->json([
                'success' => true,
                'data' => $administrator
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

}
