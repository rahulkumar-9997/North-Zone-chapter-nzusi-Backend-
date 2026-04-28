<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login first.');
        }
        $user = Auth::guard('web')->user();
        if (!$user->is_admin) {
            return redirect()
                ->route('dashboard') 
                ->with('error', 'Access denied! Admins only.');
        }
        return $next($request);
    }
}
