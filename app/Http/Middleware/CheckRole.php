<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            // Unauthorized - maybe redirect to their own dashboard or abort
            $role = Auth::user()->role;
            if ($role === 'kasir') {
                return redirect('/kasir');
            } elseif ($role === 'owner') {
                return redirect('/owner');
            }
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
