<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeetingConferenceController extends Controller
{
    public function index()
    {
        return Inertia::render('Meeting/MeetingHome');
    }

    public function createRoom(Request $request) {
        try {
            // First, check if the room exists
            $checkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DAILY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->get('https://' . env('DAILY_DOMAIN') . '/api/v1/rooms/' . $request->name);

            // If room exists, return its URL for joining
            if ($checkResponse->successful()) {
                $room = $checkResponse->json();
                return response()->json([
                    'url' => $room['url'],
                    'name' => $request->name
                ]);
            }

            // If room doesn't exist, create a new one
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DAILY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://' . env('DAILY_DOMAIN') . '/api/v1/rooms', [
                'name' => $request->name,
                'privacy' => 'public',
                'properties' => [
                    'enable_chat' => true,
                    'enable_screenshare' => true,
                    'start_video_off' => false,
                    'start_audio_off' => false,
                    'max_participants' => 10,
                    'enable_recording' => 'cloud',
                    'enable_knocking' => false,
                    'enable_prejoin_ui' => true,
                    'enable_network_ui' => true,
                    'enable_pip_ui' => true,
                    'lang' => 'en'
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Daily.co room creation failed', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return response()->json(['error' => 'Failed to create room'], 500);
            }

            $newRoom = $response->json();
            return response()->json([
                'url' => $newRoom['url'],
                'name' => $request->name
            ]);

        } catch (\Exception $e) {
            Log::error('Daily.co room operation error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to process room operation'], 500);
        }
    }

    public function joinRoom(Request $request, $name = null) {
        $meetingName = $name ?? $request->name;
        
        if (!$meetingName) {
            return redirect()->route('meeting.home')->with('error', 'Meeting name is required');
        }

        // For POST requests, redirect to the meeting URL
        if ($request->isMethod('post')) {
            return redirect()->route('meeting-room', ['name' => $meetingName]);
        }

        // For GET requests, render the meeting page
        return Inertia::render('Meeting/Meeting', [
            'meetingName' => $meetingName,
            'params' => [
                'name' => $meetingName
            ]
        ]);
    }
} 