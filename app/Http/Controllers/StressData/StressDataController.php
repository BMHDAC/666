<?php

namespace App\Http\Controllers\StressData;

use App\Consts\Schema\DBStressDataFields;
use App\Http\Controllers\Controller;
use App\Models\StressData;
use App\Models\User;
use App\Structs\Stress_Data\StressDataStruct;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


/**
 * Controller basic structure:
 * Validating input
 * Validating found data
 * Try querying -> Catch Exception
 * Return response
 */

class StressDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = StressData::get_all();
        if ($data->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Data Found'
            ], 404);
        }
    }


    public function get_stress_data_by_user_id(string $user_id)
    {
        if (User::getUser($user_id) == null) {
            return response()->json([
                'status' => 404,
                'message' => 'User Not Found'
            ], 404);
        }

        try {
            $data = StressData::get_by_user_id($user_id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }

        if ($data->count() == 0) {
            return response()->json([
                'status' => 404,
                'message' => "No data found"
            ], 404);
        }

        return response()->json([
            'status' => 200,
            "message" => "Data found",
            'data' => $data
        ]);
    }

    public function get_stress_data_by_user_id_at_date(Request $request, $user_id)
    {
        // Check if the user_id exists

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



        // Try query data by date provided
        // Return error status with query exception

        try {
            $found_data = StressData::get_by_user_id_at_date($user_id, $date_time_validator);
            if ($found_data->count() == 0) {
                return response()->json([
                    'status' => 404,
                    'message' => "No data found"
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }


        return response()->json([
            'status' => 200,
            'message' => "Data found from user " . $user_id . " at " . $request->input("year") . "-" . $request->input("month") . "-" . $request->input("day"),
            'data' => $found_data,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function post_stress_data(Request $request)
    {
        $rule = [
            'user_id' => 'required',
            'stress_level' => 'required',
            'datetime' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'average_heart_rate' => 'required',
            'step_count' => 'required',
            'device_id' => 'required',
        ];
        $message = [
            'user_id.required' => trans('v1/default.error_user_id_required'),
            'stress_level.required' => trans('v1/default.error_stress_level_required'),
            'datetime.required' => trans('v1/default.error_datetime_required'),
            'latitude.required' => trans('v1/default.error_latitude_required'),
            'longitude.required' => trans('v1/default.error_longitude_required'),
            'average_heart_rate.required' => trans('v1/default.error_average_heart_rate_required'),
            'step_count.required' => trans('v1/default.error_step_count_required'),
            'device_id.required' => trans('v1/default.error_device_id_required'),
        ];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => firstError($validator->getMessageBag()->toArray())
            ], 500);
        }

        if (User::getUser($request->input("user_id")) == null) {
            return response()->json([
                'status' => 404,
                'message' => 'User Not Found'
            ], 404);
        }

        if (Carbon::canBeCreatedFromFormat($request->input("datetime"), 'Y-m-d H:i:s') == false) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid Datetime Format'
            ], 400);
        }


        $data = [
            "id"               => Str::lower(Str::ulid()->toBase32()),
            "user_id"          => $request->input("user_id"),
            "stress_level"     => $request->input("stress_level"),
            "datetime"         => $request->input("datetime"),
            "latitude"         => $request->input("latitude"),
            "longitude"        => $request->input("longitude"),
            "average_heart_rate" => $request->input("average_heart_rate"),
            "step_count"       => $request->input("step_count"),
            "device_id"        => $request->input("device_id"),
        ];
        $data = normalizeToRedisViaArray($data, DBStressDataFields::STRESS_DATA);

        if (!$data) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid Data'
            ], 400);
        }


        $stress_data_struct = new StressDataStruct($data);
        try {
            StressData::add_stress_data($data);
            return response()->json([
                'status' => 200,
                'data' => $stress_data_struct
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function delete_entry_by_id(string $id)
    {
        $found_data = StressData::get_entry_by_id($id);

        if ($found_data == null) {
            return response()->json([
                'status' => 404,
                'message' => "No data found"
            ]);
        }
        try {
            StressData::delete_entry_by_id($id);
            return response()->json([
                'status' => 200,
                'message' => "Data deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
