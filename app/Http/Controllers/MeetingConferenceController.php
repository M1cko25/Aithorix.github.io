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

            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Daily.co room creation error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to create room'], 500);
        }
    }

    public function joinRoom(Request $request, $name = null) {
        // Handle both POST and GET requests
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