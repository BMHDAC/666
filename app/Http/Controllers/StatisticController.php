<?php

namespace App\Http\Controllers;
use App\Models\Statistic;
use Illuminate\Http\JsonResponse;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Statistic::get_all();
        if ($data->count() == 0 ) {
            return response()->json([
                "status"=> 404,
                "message"=> "No data found"
            ],404);
        };
        return response()->json([
            "status"=>200,
            "data"=> $data
        ]);
    }
}
