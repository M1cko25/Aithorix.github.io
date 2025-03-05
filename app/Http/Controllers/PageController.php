<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function showForm(): Response
    {
        return Inertia::render('Scrum');
    }
}
