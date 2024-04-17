<?php

namespace App\Http\Controllers\Post;

use App\Consts\DateFormat;
use App\Consts\Schema\DBPostFields;
use App\Http\Controllers\Controller;
use App\Libs\QueryFields;
use App\Models\Post\PostModel;
use App\Structs\Post\PostStruct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function AddPost(Request $request): JsonResponse
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
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'created_at' => now()->format(DateFormat::TIMESTAMP_DB),

            ];
            $data = normalizeToRedisViaArray($data, DBPostFields::POST);
            if ($data) {
                PostModel::doAdd($data);
                $post_struct = new PostStruct($data);
                $json = [
                    'data' => $post_struct->toArray(),
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

    public function getPosts(Request $request):JsonResponse{
        $rule = [];
        $message = [];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->errors()->count()) {
            $json = [
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
            $field_access = new QueryFields($request,DBPostFields::POST);

            $filter_data = [
                'fields' => $field_access->select,
                ...pageLimit($request),
                'sort_by' => $request->input('sort_by') ?? 'created_at',
                'sort' => $request->input('sort') ?? 'asc',
                'search_by' => $request->input('search_by') ?? null,
                'key' => $request->input('key') ?? null,
            ];
            if($data = PostModel::doGet($filter_data)){
                foreach ($data as $item){
                    $post_struct = new PostStruct($item);
                    $response_data[] = $post_struct->toArray([]);
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
