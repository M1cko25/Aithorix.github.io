<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ProjectMembers;
use Illuminate\Support\Facades\Auth;

class ProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->query('id');
        
        if (!$projectId) {
            return redirect('/home');
        }

        $hasAccess = ProjectMembers::where('project_id', $projectId)
            ->where('user_id', Auth::user()->id)
            ->exists();

        if (!$hasAccess) {
            return redirect('/home')->with('error', 'You do not have access to this project');
        }

        return $next($request);
    }   
}
