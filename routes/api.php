<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/underground_lines', function () {

    $allLines_raw = [];

    try{

        //Getting all the metro lines
        $response = Http::get(env('UNDERGROUND_LINES_URL') . env('UNDERGROUND_LINES_URL_SECURIZED'));
        $allLines_raw = $response->json();

    }
    catch(\Exception $e){

        return $e->getMessage();

    }

    $allLines = [];

    //Just getting the features that contain the line properties that we need
    foreach ($allLines_raw["features"] as $line){

        $aux_arrayLines = [];

        $aux_arrayLines["lineCode"] = $line["properties"]["CODI_LINIA"];
        $aux_arrayLines["lineName"] = $line["properties"]["NOM_LINIA"];

        try{

            $response = Http::get(env('UNDERGROUND_LINES_URL') . "/" . $aux_arrayLines['lineCode'] . env('UNDERGROUND_STATIONS_URL') . env('UNDERGROUND_LINES_URL_SECURIZED'));

        }
        catch(\Exception $e){
    
            return $e->getMessage();
    
        }
        
        $currentStations = $response->json();
        
        //Getting each station per line and adding it to the correspondent line
        foreach ($currentStations["features"] as $station){

            $aux_arrayStations = [];

            $aux_arrayStations["stationCode"] = $station["properties"]["ID_ESTACIO"];
            $aux_arrayStations["stationName"] = $station["properties"]["NOM_ESTACIO"];

            $aux_arrayLines["stations"][] = $aux_arrayStations;

        }


        array_push($allLines, $aux_arrayLines);

    }

    return json_encode($allLines);

});
