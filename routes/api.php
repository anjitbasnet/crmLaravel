<?php

use App\Http\Resources\TodayResource;
use App\Http\Resources\UpcomingResource;
use App\Models\Today;
use App\Models\Upcoming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// ** Upcoming Task ** //

//Get all upcoming task
Route::get("/upcoming", function(){
    $upcoming = Upcoming::all();

    return UpcomingResource::collection($upcoming);
});

//Add new task
Route::post("/upcoming", function (Request $request){
    return Upcoming::create([
        'title' => $request->title,
        'taskId' => $request->taskId,
        'waiting' => $request->waiting

    ]);
});

//Delete upcoming task
Route::delete("/upcoming/{taskId}", function($taskId) {
    DB::table('upcomings')->where('taskId', $taskId)->delete();

    return 204;
});

// ** Today Task **//
Route::get("/today", function(){
    $today = Today::all();

    return TodayResource::collection($today);
});

//Add Today task
Route::post("/today", function (Request $request){
    return Today::create([
        'id' => $request->id,
        'title' => $request->title,
        'taskId' => $request->taskId,

    ]);
});

//Delete today task
Route::delete("/today/{taskId}", function($taskId){
    DB::table('todays')->where('taskId', $taskId)->delete();

    return 204;
});