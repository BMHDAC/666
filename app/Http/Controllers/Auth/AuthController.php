<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Structs\Struct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTGuard;


class AuthController extends Controller
{
    protected User $user_info;

    public function index(Request $request): JsonResponse
    {
        $rule = [
            'username' => 'required|between:3,30',
            'password' => 'required|between:6,30'
        ];

        $message = [
            'username.required' => trans('v1/auth.error_username_required'),
            'username.between'  => trans('v1/auth.error_username_between', [
                'min' => 3,
                'max' => 30
            ]),
            'password.required' => trans('v1/auth.error_password_required'),
            'password.min'      => trans('v1/auth.error_password_between', [
                'min' => 6,
                'max' => 30
            ])
        ];

        $validator = $this->_validate($request, $rule, $message);
        if ($validator->errors()->count()) {
            $json = [
                'error' => firstError($validator->getMessageBag()->toArray())
            ];
        } else {
            $json = $this->createSession($request);
        }

        return resJson($json);
    }
    public function createSession(Request $request): array
    {
        /**@var $user User */
//        $user_struct = new UserStruct($this->user_info->getAttributes());

        $token = auth()->attempt($request->only('username', 'password'));

//        LoginEvent::dispatch($user_struct->id, $token, StateLogout::ACTIVATE, Carbon::createFromTimestamp($this->guard()->getPayload()->get('exp')));

        $user        = auth()->user();
        $user_struct = $user->struct();

        return [
            'data' => [
                ...$user_struct->toArray([
//                    Struct::OPT_CHANGE => [
//                        'image' => ['getImage']  // process image by function inside struct
//                    ],
                    Struct::OPT_IGNORE => [
                        'status',
                        'password'
                    ]
                ]),
                'access_token' => [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'created_at' => now(),
                    'expires_in' => $this->guard()->getPayload()->get('exp') - $this->guard()->getPayload()->get('iat'),
                ],
            ]
        ];
    }

    public function guard(): JWTGuard
    {
        /**@var $guard JWTGuard */
        $guard = Auth::guard();

        return $guard;
    }

    public function me(Request $request): JsonResponse
    {
        if ($this->getUser($request)) {
            $user_struct = $this->user->struct();
        } else {

            return resJson([
                'code'  => 200, //400,
                'error' => [
                    'user' => trans('v1/auth.error_username_not_exist'),
                ],
            ]);
        }

        return resJson([
            'data' => $user_struct->toArray([
//                Struct::OPT_CHANGE => [
//                    'image' => ['getImage']  // process image by function inside struct
//                ],
                Struct::OPT_IGNORE => [
                    'status',
                    'password'
                ]
            ])
        ]);
    }

    protected function _validate(Request $request, ?array $rule = [], ?array $message = []): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        $validator = Validator::make($request->all(), $rule, $message);
        if (!$validator->fails()) {
            $user = User::getUserByName($request->input('username'), ['*']);
            if ($user instanceof User) {
                if (!$user->getAttribute('status')) {
                    $validator->errors()->add('username', trans('v1/auth.error_username_status'));
                } else if (!Hash::check($request->input('password'), $user->getAttribute('password'))) {
                    $validator->errors()->add('password', trans('v1/auth.error_password_incorrect'));
                } else {
                    $this->user_info = $user;
                }
            } else {
                $validator->errors()->add('username', trans('v1/auth.error_username_not_exist'));
            }
        }

        return $validator;
    }

}
