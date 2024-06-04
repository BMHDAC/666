<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        try {
            $data = Statistic::get_all();
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Internal Server Error"
            ]);
        }

        if ($data->count() == 0) {
            return response()->json([
                "status" => 404,
                "message" => "No data found"
            ], 404);
        };
        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }
    public function get_avg(Request $request, $user_id)
    {
        if (User::getUser($user_id) == null || !$user_id) {
            return response()->json([
                "status" => 404,
                "message" => "User Not Found"
            ], 404);
        }

        $rule = [
            "day" => "required",
            "month" => "required",
            "year" => "required",
        ];

        $message = [
            "day.required" => trans('v1/default.error_day_required'),
            "month.required" => trans('v1/default.error_month_required'),
            "year.required" => trans('v1/default.error_year_required'),
        ];
        $validator = $this->_validate($request, $rule, $message);

        // Validate input

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => $validator->errors()
            ], 500);
        }


        // Validate datetime input : (YYYY-MM-DD)
        if (checkdate($request->input("month"), $request->input("day"), $request->input("year")) == false) {
            return response()->json([
                'status' => 400,
                'message' => "Invalid date format"
            ], 400);
        }

        $date_time_validator = DateTime::createFromFormat('Y-m-d', $request->input("year") . "-" . $request->input("month") . "-" . $request->input("day"));

        try {
            $found_data = Statistic::get_avg_of_user($user_id, $date_time_validator);
            if ($found_data->count() == 0) {
                return response()->json([
                    "status" => 404,
                    "message" => "No data found"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status"  => 500,
                "message" => "Internal Server Errors"
            ], 500);
        }
        return response()->json([
            "status" => 200,
            "data" => $found_data
        ]);
    }
}
