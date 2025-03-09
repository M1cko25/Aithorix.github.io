<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    public function generateToken(Request $request)
    {
        $appId = (int)env('ZEGO_APP_ID');
        $serverSecret = env('ZEGO_SERVER_SECRET');
        $userId = $request->input('user_id');
        $roomId = $request->input('room_id');

        if (!$appId || !$serverSecret || !$userId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $timestamp = (int)(microtime(true) * 1000); // Current timestamp in milliseconds
            $expired = $timestamp + (60 * 60 * 1000); // Token valid for 1 hour

            // Create the payload
            $payload = [
                'app_id' => $appId,
                'user_id' => $userId,
                'nonce' => uniqid(),
                'ctime' => $timestamp,
                'expire' => $expired,
                'payload' => ''
            ];

            if ($roomId) {
                $payload['room_id'] = $roomId;
            }

            // Sort payload keys
            ksort($payload);

            // Create string to sign
            $stringToSign = '';
            foreach ($payload as $key => $value) {
                $stringToSign .= $key . '=' . $value;
            }

            // Create signature
            $signature = hash_hmac('sha256', $stringToSign, $serverSecret);

            // Create token string
            $token = sprintf(
                '%04x%s%s%s%s',
                3,  // version 3
                dechex($timestamp),
                dechex($expired),
                $signature,
                base64_encode(json_encode($payload))
            );

            Log::info('Token generated successfully', [
                'user_id' => $userId,
                'room_id' => $roomId,
                'timestamp' => $timestamp
            ]);

            return response()->json([
                'token' => $token,
                'timestamp' => $timestamp,
                'expired' => $expired
            ]);
        } catch (\Exception $e) {
            Log::error('Token generation error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'room_id' => $roomId
            ]);
            return response()->json([
                'error' => 'Failed to generate token: ' . $e->getMessage()
            ], 500);
        }
    }
}
