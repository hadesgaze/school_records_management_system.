<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Check if user has admin role/privileges
        // Adjust this logic based on how you determine admin users
        // Example 1: If you have a 'role' column in users table
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // OR Example 2: If you're using role/permission packages like Spatie
        // if (!Auth::user()->hasRole('admin')) {
        //     abort(403, 'Unauthorized access.');
        // }

        // OR Example 3: If you have an 'is_admin' boolean column
        // if (!Auth::user()->is_admin) {
        //     abort(403, 'Unauthorized access.');
        // }

        return $next($request);
    }
}