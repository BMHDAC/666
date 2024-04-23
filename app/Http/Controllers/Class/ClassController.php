<?php

namespace App\Http\Controllers\Class;

use App\Consts\DateFormat;
use App\Consts\Schema\DBClassFields;
use App\Http\Controllers\Controller;
use App\Libs\QueryFields;
use App\Models\Class\ClassModel;
use App\Structs\Class\ClassStruct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function AddClass(Request $request): JsonResponse
    {
        $rule = [];
        $message = [];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->errors()->count()) {
            $json = [
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
            $data = [
                'id' => Str::lower(Str::ulid()->toBase32()),
                'name_student' => $request->input('name_student'),
                'class' => $request->input('class'),
                'created_at' => now()->format(DateFormat::TIMESTAMP_DB),

            ];
            $data = normalizeToRedisViaArray($data, DBClassFields::CLASSROOM);
            if ($data) {
                ClassModel::doAdd($data);
                $class_struct = new ClassStruct($data);
                $json = [
                    'data' => $class_struct->toArray(),
                    'code' => 200,
                ];
            } else {
                $json = [
                    'error' => "loi",
                    'code' => 400,
                ];
            }
        }

        return resJson($json);
    }

    public function getClass(Request $request):JsonResponse{
        $rule = [];
        $message = [];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->errors()->count()) {
            $json = [
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
            $field_access = new QueryFields($request, DBClassFields::CLASSROOM);

            $filter_data = [
                'fields' => $field_access->select,
                ...pageLimit($request),
                'sort_by' => $request->input('sort_by') ?? 'created_at',
                'sort' => $request->input('sort') ?? 'asc',
                'search_by' => $request->input('search_by') ?? null,
                'key' => $request->input('key') ?? null,
            ];
            if($data = ClassModel::doGet($filter_data)){
                foreach ($data as $item){
                    $class_struct = new ClassStruct($item);
                    $response_data[] = $class_struct->toArray([]);
                }
            }
            $json = [
                'items' => $response_data ?? null,
                '_meta_data' => ResMetaJson($data),
                'code' => 200,
            ];
        }

        return resJson($json);
    }
}
