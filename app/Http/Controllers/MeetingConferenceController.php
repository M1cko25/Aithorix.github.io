<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MeetingCodes;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Meetings;
use App\Models\MeetingParticipants;

class MeetingConferenceController extends Controller
{
    public function createRoom(Request $request) {
        try {
            $meetingCode = MeetingCodes::generateUniqueCode();
            $fullRoomName = $request->name . '-' . str_replace('-', '', $meetingCode);

            $checkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DAILY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->get('https://' . env('DAILY_DOMAIN') . '/api/v1/rooms/' . $fullRoomName);

            if ($checkResponse->successful()) {
                return response()->json(['error' => 'Please try again'], 409);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DAILY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://' . env('DAILY_DOMAIN') . '/api/v1/rooms', [
                'name' => $fullRoomName,
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
                return response()->json(['error' => 'Failed to create room'], 500);
            }
            Log::info('room name: ' . $fullRoomName);
            
            $meetCode = MeetingCodes::create([
                'meeting_name' => $request->name,
                'code' => $meetingCode,
                'created_by' => Auth::id(),
                'expires_at' => now()->addDays(1),
                'project_id' => $request->projectId
            ]);
            Meetings::create([
                'name' => $request->name,
                'code_id' => $meetCode->id,
                'status' => 'started',
                'date' => now(),
                'start_time' => now(),
                'end_time' => now()->addHours(1),
                'project_id' => $request->projectId,
                'creator_id' => Auth::id()
            ]);
            
            $newRoom = $response->json();
            return response()->json([
                'url' => $newRoom['url'],
                'name' => $request->name,
                'code' => str_replace('-', '', $meetingCode)
            ]);

        } catch (\Exception $e) {
            Log::error('Daily.co room operation error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to process room operation'], 500);
        }
    }

    public function joinRoom(Request $request, $name = null) {
        try {
            // For POST requests (joining with code)
            if ($request->isMethod('post')) {
                $meetingCode = $request->code;
                
                if (!$meetingCode) {
                    return redirect()->back()->with('error', 'Meeting code is required');
                }

                // Find the meeting by code
                $meetingRecord = MeetingCodes::where('code', $meetingCode)
                    ->where('expires_at', '>', now())
                    ->first();

                if (!$meetingRecord) {
                    Log::error('Invalid or expired meeting code');
                    return redirect()->back()->with('error', 'Invalid or expired meeting code');
                }

                // Get the actual meeting record
                $meeting = Meetings::where('code_id', $meetingRecord->id)->first();
                
                if (!$meeting) {
                    Log::error('Meeting not found for code: ' . $meetingCode);
                    return redirect()->back()->with('error', 'Meeting not found');
                }

                $project = Project::where('id', $meetingRecord->project_id)->first();

                // Create participant record with the correct meeting ID
                MeetingParticipants::create([
                    'meeting_id' => $meeting->id, // Use the actual meeting ID
                    'user_id' => Auth::id(),
                    'status' => 'On Time'
                ]);

                $fullRoomName = $meetingRecord->getFullRoomName();
                Log::info('Meeting code: ' . $fullRoomName);
                return redirect()->route('meeting-room', ['name' => $fullRoomName, 'project' => $project]);
            }

            // For GET requests (accessing meeting room directly)
            if (!$name) {
                Log::error('Invalid meeting URL 1');
                return redirect()->back()->with('error', 'Invalid meeting URL');
            }
            $roomName = $name;
            $codeParts = explode('-', $roomName);
            
            if (count($codeParts) !== 2) {
                Log::error('Invalid meeting URL 2: name: ' . $name . ' codeParts: ' . $codeParts[0] . ' codeParts: ' . $codeParts[1]);
                return redirect()->back()->with('error', 'Invalid meeting URL');
            }

            $meetingName = $codeParts[0];
            $codeWithoutHyphen = $codeParts[1];

            $code = substr($codeWithoutHyphen, 0, 4) . '-' . substr($codeWithoutHyphen, 4);
            
            $meetingRecord = MeetingCodes::where('code', $code)
                ->where('meeting_name', $meetingName)
                ->where('expires_at', '>', now())
                ->first();

            if (!$meetingRecord) {
                Log::error('Invalid or expired meeting, code: ' . $code);
                return redirect()->back()->with('error', 'Invalid or expired meeting');
            }

            // Get the actual meeting record
            $meeting = Meetings::where('code_id', $meetingRecord->id)->first();
            
            if (!$meeting) {
                Log::error('Meeting not found for code: ' . $code);
                return redirect()->back()->with('error', 'Meeting not found');
            }

            // Create participant record with the correct meeting ID
            MeetingParticipants::create([
                'meeting_id' => $meeting->id,
                'user_id' => Auth::id(),
                'status' => 'On Time'
            ]);
                
            $project = Project::where('id', $meetingRecord->project_id)->first();

            return Inertia::render('Meeting/Meeting', [
                'meetingName' => $name,
                'project' => $project,
                'params' => [
                    'name' => $name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error joining meeting', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to join meeting');
        }
    }
} 