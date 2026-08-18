<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEnabled
{
    /**
     * Disabling a user is only checked at login time, so an already
     * authenticated session would survive. Terminate it on the next request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only an explicit false means "disabled". A null is an attribute that
        // was never loaded, and must not lock anybody out.
        if ($user !== null && $user->is_enabled !== null && !$user->is_enabled) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'تم تعطيل هذا الحساب. يرجى مراجعة إدارة النظام.',
            ]);
        }

        return $next($request);
    }
}
