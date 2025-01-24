<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplatesController extends Controller
{
    public function templateSelected(Request $request)
    {
        return Inertia::render('ProjectCreation', [
            'selectedTemplate' => $request,
        ]);
    }
}
