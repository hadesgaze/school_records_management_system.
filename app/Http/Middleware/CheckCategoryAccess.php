<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Category;

class CheckCategoryAccess
{
    public function handle(Request $request, Closure $next)
    {
        $category = $request->route('category');
        
        if ($category instanceof Category) {
            $userRole = auth()->user()->role; // Assuming you have a 'role' column in users table
            
            if (!$category->isAccessibleBy($userRole)) {
                abort(403, 'You do not have permission to access this category.');
            }
        }
        
        return $next($request);
    }
}