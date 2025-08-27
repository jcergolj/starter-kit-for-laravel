<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setupComplete = (bool) Setting::get('setup_complete', false) && User::query()->exists();
        
        if (!$setupComplete && !$request->routeIs('register', 'register.store')) {
            return redirect()->route('register');
        }
        
        if ($setupComplete && $request->routeIs('register', 'register.store')) {
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
