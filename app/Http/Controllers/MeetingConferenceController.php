<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingConferenceController extends Controller
{
    public function index()
    {
        return Inertia::render('MeetingConference', [
            'title' => 'Meeting Conference',
        ]);
    }
} 