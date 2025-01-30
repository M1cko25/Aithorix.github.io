<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\User;

class UserController extends Controller
{
    public function searchUsers(Request $request)
    {
        $search = $request->input('search');
        
        $users = User::where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('google_email', 'LIKE', "%{$search}%")
                    ->orWhere('slack_email', 'LIKE', "%{$search}%")
                    ->get(['id', 'name', 'email', 'google_email', 'slack_email' , 'avatar']);
                    
        return response()->json($users);
    }
}
