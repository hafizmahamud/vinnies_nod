<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Activity;
use App\User;


class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $data = [];
        $user = Auth::user();

        // $id   = $request->get('id');
        // $activity = Activity::all()->where('subject_type', 'App\User')->where('subject_id', $id);
        $activity = Activity::all()->take(10);
        
        $data['activity'] = $activity->map(function ($activity) {
            return [
                'id'           => $activity->id,
                'event'        => $activity->event,
                'subjectID'    => $activity->subject_id,
                'properties'   => $activity->properties,
                'updated_at'   => $activity->updated_at,
                'updated_by'   => $activity->causer_id,
            ];
        });
        
        // return $data;
    }
}
