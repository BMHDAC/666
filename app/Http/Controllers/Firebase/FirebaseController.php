<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseController extends Controller
{
    private Messaging $notification;
    private Database $database;

    public function __construct()
    {
        $firebase = (new Factory())
            ->withDatabaseUri(env('FIREBASE_DATABASE'))
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->notification = $firebase->createMessaging();
        $this->database = $firebase->createDatabase();
    }

    public function set_token(Request $request) : Response
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
    public function send_notification(array $data, string $user_id, array $notification)
    {
        if ( User::getUser($user_id) == null)  {
            return ;
        }
        $token = $this->database->getReference(path: "/user_token/" . $user_id)->getChild("fcm_token")->getValue();
        if ($token == null || $token == "") {
            return;
        }
        $title = "To" . $user_id;
        $body = $notification;
        $imageUrl = '';

        $sending_notification = Notification::fromArray([
            "title" => $title,
            "body" => $body,
            "imageUrl" => $imageUrl
        ]);
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($sending_notification);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Internal Server Error"
            ]);
        }


        $message;
        try {
            $this->notification->send($message);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 505,
                "message" => $e->getMessage()
            ]);
        }
        return response()->json([
            "status" => 200,
            "message" => "Success"
        ]);

       /* $token = $this->database->getReference("/user_token/" . $user_id)->getChild("fcm_token"); if ($token = null || $token == "") { */
        /*     return ; */
        /* } */
        /* $message = CloudMessage::withTarget('token', $token); */
        /**/
        /* $message = CloudMessage::fromArray([ */
        /*     'id' => $user_id, */
        /*     'data' => $data */
        /* ]); */
        /**/
        /* return $this->notification->send($message); */

    }
    public function test_notification(Request $request)
    {
        if (User::getUser($request->input("user_id")) == null) {
            return response()->json([
                'status' => 404,
                'message' => 'User Not Found'
            ], 404);
        }
        $rule = [
            "user_id" => "required",
        ];

        $message = [
            "user_id.required" => trans('v1/default.error_user_id_required'),
        ];
        $validator = $this->_validate($request, $rule, $message);
        if ($validator->fails()) {
            return response()->json([
                "status" => 503,
                "message" => $validator->errors()
            ]);
        }
        $token = $this->database->getReference("/user_token/" . $request->input("user_id"))->getChild("fcm_token")->getValue();

        if ($token == "" || $token == null) {
            return response()->json([
                "status" => 403,
                "message" => "Not Authorized"
            ], 403);
        }

        $title = 'Test Notification';
        $body = 'Test Notification';
        $imageUrl = 'https://picsum.photos/400/200';

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
        ]);
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Internal Server Error"
            ]);
        }


        try {
            $this->notification->send($message);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 505,
                "message" => $e->getMessage()
            ]);
        }
        return response()->json([
            "status" => 200,
            "message" => "Success"
        ]);
    }

    //TODO! Broadcasting to others realated users;
    /* public function broadcast_notifications(array $data, array $user_id_list) */
    /* { */
    /* } */

    public function set_user_on_register(string $user_id)
    {
        return $this->database->getReference("/user_token/" . $user_id)->set([
                "fcm_token" => ""
        ]);
        // TODO!: Validate database data was successfully pushed, if not throw error
        // This function is gonna be use during register process
    }
    public function delete_user(string $user_id)
    {
        return $this->database->getReference("/user_token/" . $user_id)->remove();
    }
}
