<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event_map;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        $event_map = Event_map::all();
        $current_time = Carbon::now();
        
        $event_map = array_map(function($event_map) use ($current_time){

            //Get Diff in hours between current time and event time
            $event_time = Carbon::parse($event_map['created_at']);
            $diff_in_hours = $event_time->diffInHours($current_time);
            
            //if source is null, dont show it Or if event is older than 24 hours, dont show it
            if($event_map['source'] !== null && $diff_in_hours < 24){
                return [
                    'title' => $event_map['title'],
                    'data_id' => $event_map['data_id'],
                    'data_link' => $event_map['data_link'],
                    'lat' => $event_map['lat'],
                    'lng' => $event_map['lng'],
                    'source' => $event_map['source'],
                ];
            }
        },$event_map->toArray());
        
        //array values to remove null values
        $event_map = array_values(array_filter($event_map));
            

        $event_map = json_encode($event_map);

        return view('dashboard.index',['event_map' => $event_map]);
    }
}
