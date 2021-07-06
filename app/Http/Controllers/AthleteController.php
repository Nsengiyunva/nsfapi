<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Result;
use App\Models\Medal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AthleteController extends Controller
{
    public function add_athlete(Request $request) {
        $athlete = new Athlete;
        $athlete->first_name = $request->first_name;
        $athlete->surname = $request->surname;
        $athlete->middle_name = $request->middle_name;
        $athlete->gender = $request->gender;
        $athlete->date_of_birth = $request->dob;
        $athlete->athlete_type = $request->athlete_type;
        $athlete->discipline = $request->athlete_type;
        $athlete->institution = $request->institution;
        $athlete->profile_picture = $request->profile_picture;
        $athlete->bio = $request->bio;

        $athlete->save();

        if( $athlete->id ){
            return response()->json([
                'success' => true,
                'data' => $athlete
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_athlete_result(Request $request) {
        $athlete = new Result;
        $athlete->athlete_id = $request->athlete_id;
        $athlete->rank = $request->rank;
        $athlete->event = $request->event;
        $athlete->time = $request->time;
        $athlete->medal = $request->medal;
        $athlete->pool_length = $request->pool_length;
        $athlete->age = $request->age;
        $athlete->competition = $request->competition;
        $athlete->competition_country = $request->competition_country;
        $athlete->date = $request->date;

        $athlete->save();

        if( $athlete->id ){
            return response()->json([
                'success' => true,
                'data' => $athlete
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function add_medal(Request $request) {
        $athlete = new Medal;
        $athlete->athlete_id = $request->athlete_id;
        $athlete->event = $request->event;
        $athlete->medal = $request->medal;
        $athlete->date = $request->date;

        $athlete->save();

        if( $athlete->id ){
            return response()->json([
                'success' => true,
                'data' => $athlete
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

}
