<?php

namespace App\Http\Controllers\Post;

use App\Consts\DateFormat;
use App\Consts\Schema\DBPostFields;
use App\Http\Controllers\Controller;
use App\Libs\NorIntoDB;
use App\Libs\QueryFields;
use App\Models\Post\PostModel;
use App\Structs\Post\PostStruct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
                'user_id' => $request->user() -> id,
                'status' => 1,
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
    public function EditPost(Request $request, string $post_id): JsonResponse
    {
        /** @var $post PostModel
         * @var $post_struct PostStruct
         * */
        $rule = [
//            'name'        => 'between:3,100',
//            'type'        => 'in:image,video,gif',
//            'description' => 'between:3,10000',
//            'lat'         => 'required|numeric',
//            'lng'         => 'required|numeric',
        ];

        $message = [
//            'name.between' => trans('v1/default.error_name_between', [
//                'min' => 3,
//                'max' => 100
//            ]),
//            'type.in'      => trans('v1/default.error_type_in', [
//                'image', 'video', 'gif'
//            ]),
//            'lat.required' => trans('v1/default.error_latitude_required'),
//            'lng.required' => trans('v1/default.error_longitude_required'),
//            'lat.numeric'  => trans('v1/default.error_latitude_numeric'),
//            'lng.numeric'  => trans('v1/default.error_longitude_numeric'),
        ];

        $validator = $this->_validate($request, $rule, $message);

        if ($validator->errors()->count()) {

            $json = [
                'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } //show error
        else if (!$post = PostModel::doGetById($post_id, ['*'])) {
            $json = [
                'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                'error' => [
                    'id' => trans('v1/default.error_id_exists')
                ]
            ];
        } else {
            $post_struct = $post->struct();
            if ($post_struct->status == 0) {
                $json = [
                    'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                    'error' => [
                        'warning' => trans('v1/default.error_post_delete')
                    ]
                ];
            } //post has been deleted
            else{
                $post_data = normalizeToRequest($request->input(), DBPostFields::POST);
                $nor_into = new NorIntoDB();

                if (!$nor_into->viaDB($post_data, $post->getAttributes())) {
                    $json = [
                        'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                        'error' => [
                            'warning' => trans('v1/default.error_insert')
                        ]
                    ];
                } // nothing change
                else {
                    $update       = $nor_into->getData();

//                    if (isset($update['media'])) {
//                        $media = json_decode($update['media'], true);
//                        foreach ($media as &$value) {
//                            if (explode('/', $value['mime'])[0] == 'image')
//                                $value = array_merge($value, ['cache' => ['960x540']]);
//                        }
//                        $update['media'] = $media;
//                    }// process media
                    if (!isset($update['updated_at'])) {
                        $update['updated_at'] = now()->format(DateFormat::TIMESTAMP_DB);
                    }

                    PostModel::doEdit($update, $post);

                    $post = PostModel::doGetById($post_id, ['*']);
                    $post_struct = $post->struct();

                    $data = $post_struct->toArray([
//                        Struct::OPT_CHANGE => [
//                            'media' => ['getMedia']
//                        ]
                    ]);

                    foreach ($update as $key => &$value) {
                        if (array_key_exists($key, $data)) {
                            $value = $data[$key];
                        }
                    }

                    $json = [
                        'code' => SymfonyResponse::HTTP_OK,
                        'data' => $update,
                    ];
                }
            }
        }

        return resJson($json);
    }
    public function deletePost(Request $request, string $post_id): JsonResponse
    {
        /**@var post_struct PostStruct
         * @var $user_access UserStruct
         * @var $post PostModel
         */

        $rule = [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ];

        $message = [
            'lat.required' => trans('v1/default.error_latitude_required'),
            'lng.required' => trans('v1/default.error_longitude_required'),
            'lat.numeric'  => trans('v1/default.error_latitude_numeric'),
            'lng.numeric'  => trans('v1/default.error_longitude_numeric'),
        ];

        $validator = $this->_validate($request, $rule, $message);

        if ($validator->errors()->count()) {
            $json = [
                'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
            $post_media = PostMediaModel::where('id', $post_id)->first();

            if (!$post_media) {
                $json = [
                    'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                    'error' => ['id' => trans('v1/default.error_id_exists')]
                ];
            } else {
                $post_media_struct = $post_media->struct();
                $post_struct       = PostModel::getPostByCacheOrQuery($post_media_struct->post_id, ['*']);

                if ($post_media_struct->status == 0) {
                    $json = [
                        'code'  => SymfonyResponse::HTTP_BAD_REQUEST,
                        'error' => [
                            'warning' => trans('v1/default.error_post_delete')
                        ]
                    ];
                } else {
                    if ($post_media_struct->schedule_id) {
                        $post_schedule_struct = ScheduleModel::getScheduleByCacheOrQuery($post_media_struct->schedule_id, ['*']);
                        if ($post_schedule_struct->status != 0) {
                            $json = [
                                'code'  => SymfonyResponse::HTTP_FORBIDDEN,
                                'error' => [
                                    'schedule|status' => trans('v1/default.error_status_check')
                                ]
                            ];
                        } else {
                            $json = [
                                'code' => SymfonyResponse::HTTP_OK,
                            ];
                        }
                    } else {
                        $json = [
                            'code' => SymfonyResponse::HTTP_OK,
                        ];
                    }

                    $location = $request->input('lat') . ',' . $request->input('lng');
                    $post_media->delete();
                    $expires_time  = Carbon::createFromTimestamp($this->guard()->getPayload()->get("exp"));
                    $data_activity = [
                        'id'         => $post_id,
                        'expired_at' => $expires_time,
                        'location'   => $location,
                        'post_id'    => $post_media->getAttribute('post_id'),
                    ];
                }
            }
        }

        return resJson($json);
    }
}
