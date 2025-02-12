<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TimelineController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'text' => 'required|string',
            'description' => 'required|string',
            'project_id' => 'required|exists:projects,id'
        ]);

        $timeline = Timeline::create([
            'type' => $request->type,
            'text' => $request->text,
            'description' => $request->description,
            'details' => json_encode($request->details),
            'user_id' => Auth::user()->id,
            'project_id' => $request->project_id
        ]);

        return response()->json($timeline);
    }


    public function index($projectId)
    {
        $timelineItems = Timeline::where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($timelineItems);
    }
}
