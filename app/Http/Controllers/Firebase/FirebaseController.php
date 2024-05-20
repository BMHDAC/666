<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseController extends Controller
{
    private $notification;
    private $database;

    public function __construct()
    {
        $firebase = (new Factory())
            ->withDatabaseUri(env('FIREBASE_DATABASE'))
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->notification = $firebase->createMessaging();
        $this->database = $firebase->createDatabase();
    }

    public function set_token(Request $request)
    {
        $fcm_token = $request->input("fcm_token");
        if (!$fcm_token) {
            return response()->json(
                [
                    "status" => 404,
                    "message" => "Invalid or Missing FCM Token"
                ],
                404
            );
        }
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(
                [
                    "status" => 406,
                    "message" => "User has already been deleted"
                ],
                406
            );
        }

        $this->database->getReference("/user_token/" . Auth::id())
            ->set(["fcm_token" => $fcm_token]);

        return response()->json([
            'status' => 200,
            'message' => "Database Updated"
        ]);
    }

    public function update_token(Request $request)
    {
        $user = $request->user();
        if (!($user instanceof User)) {
            return response()->json([
                "status" => 404,
                "message" => "User could have been deleted"
            ]);
        }
        $rule = [
            "fcm_token" => "required",
        ];
        $messages = [
            "fcm_token.required" => trans("v1/default/error_fcm_token_required")
        ];
        $validator = $this->_validate($request, $rule, $messages);
        if ($validator->fails()) {
            return response()->json(
                [
                    "status" => 400,
                    "message" => $validator->errors()
                ]
            );
        }
        $id = Auth::id();

        $this->database->getReference("/user_token/" . $id)
            ->update(["fcm_token" => $request->input("fcm_token")]);

        return response()->json([
            "status" => 200,
            "message" => "Token updated"
        ]);
    }
    public function send_notification(array $data, string $user_id)
    {
        $token = User::get_fmc_token($user_id);
        $message = CloudMessage::fromArray([
            'token' => $token,
            'data' => $data
        ]);
        $this->notification->send($message);
    }

    public function set_user_on_register(string $user_id)
    {
        $this->database->getReference("/user_token/" . $user_id)->set([
            "fcm_token" => ""
        ]);
        return;
        // TODO!: Validate database data was successfully pushed, if not throw error
        // This function is gonna be use during register process
    }
}
