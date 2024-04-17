<?php

namespace App\Http\Controllers\Auth;


use App\Consts\DateFormat;
use App\Consts\Schema\DBUserFields;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Structs\Struct;
use App\Structs\User\UserStruct;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rule = [
            'name'             => 'required|between:3,100',
            'username'         => 'required|between:3,50',
            'email'            => 'required|between:3,50|email:rfc,dns',
            'password'         => 'required|between:4,100',
            'confirm'          => 'required|same:password',
            'address'          => 'between:4,100',
            'DoB'              => 'date',
        ];

        $message = [
            'name.required'            => trans('v1/default.error_name_required'),
            'name.between'             => trans('v1/default.error_name_between', [
                'min' => 3,
                'max' => 100
            ]),
            'username.required'        => trans('v1/default.error_username_required'),
            'username.between'         => trans('v1/default.error_username_between', [
                'min' => 3,
                'max' => 50
            ]),
            'email.required'           => trans('v1/default.error_email_required'),
            'email.between'            => trans('v1/default.error_email_between', [
                'min' => 3,
                'max' => 50
            ]),
            'email.email'              => trans('v1/default.error_email_email'),
            'password.required'        => trans('v1/default.error_password_required'),
            'password.between'         => trans('v1/default.error_password_between', [
                'min' => 4,
                'max' => 100
            ]),
            'telephone_number.between' => trans('v1/default.error_phone_between', [
                'min' => 9,
                'max' => 10
            ]),
            'confirm.same'             => trans('v1/default.error_confirm_same_password'),
            'address.between'          => trans('v1/default.error_address_between', [
                'min' => 4,
                'max' => 100,
            ]),
            'DoB.date'                 => trans('v1/default.error_DoB_format'),
        ];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->errors()->count()) {
            $json = [
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
//            $image = json_decode($request->input('image'), true);
//            if ($image) {
//                $image = array_merge($image, ['cache' => ['200x200']]);
//            }
            $data = [
                'id'               => Str::lower(Str::ulid()->toBase32()),
                'name'             => $request->input('name'),
                'username'         => $request->input('username'),
                'email'            => $request->input('email'),
                'password'         => Hash::make($request->input('password')),
                'created_at'       => now()->format(DateFormat::TIMESTAMP_DB),
                //                'DoB'        => Carbon::parse($request->input('DoB'))->format('Y-m-d'),
                'status'           => 1,
            ];

            $data = normalizeToSQLViaArray($data, DBUserFields::USERS);

            if ($data) {
                $user_struct = new UserStruct($data);
//                if ($user_struct->image) {
//                    ResMedia::handle($user_struct->image);
//                }
                User::addUserGetID($data);
//
                $data_response = $user_struct->toArray([
                    Struct::OPT_IGNORE => [
                        'password'
                    ]
                ]);

//                RegisterEvent::dispatch($id, ['password' => $data['password']]);
                $json = [
                    'data' => $data_response,
                    'code' => 200,
                ];
            } else {
                $json = [
                    'code'  => 400, //400,
                    'error' => [
                        'warning' => trans('v1/default.error_insert'),
                    ]
                ];
            }
        }
        return response()->json($json);
    }

    protected function _validate(Request $request, ?array $rule = [], ?array $message = []): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        $validator = Validator::make($request->all(), $rule, $message);

        if (!$validator->fails()) {
            if (User::checkExists([
                'username' => $request->input('username')
            ])) {
                $validator->errors()->add('username', trans('v1/auth.error_username_existed'));
            } else if (User::checkExists([
                'email' => $request->input('email')
            ])) {
                $validator->errors()->add('email', trans('v1/auth.error_username_existed'));
            }
        }

        return $validator;
    }
}
