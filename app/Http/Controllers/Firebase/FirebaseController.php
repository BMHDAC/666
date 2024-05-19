<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseController extends Controller
{
    private $firebase = (new Factory())
        ->withDatabaseUri(env('FIREBASE_DATABASE'))
        ->withServiceAccount(env('FIREBASE_CREDENTIALS'));
    private $notification;

    public function __construct()
    {
        $this->notification = $this->firebase->createMessaging();
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
        try {
            $user->update(["fcm_token" => $fcm_token]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => 500,
                    "message" => "Internal Server Error",
                    "error" => $e
                ],
                500
            );
        }

        return response()->json([
            "status" => 200,
            "message" => "FCM Token Updated"
        ], 200);
    }


    public function send_notification(array $data, string $user_id)
    {
        $token = User::get_fmc_token($user_id);
        $message = CloudMessage::fromArray([
            'token' => $token,
            'data' => $data
        ]);
        $this->notification->send($message)
    }
}
