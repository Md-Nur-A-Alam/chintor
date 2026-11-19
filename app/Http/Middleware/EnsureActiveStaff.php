<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStaff
{
    /**
     * Sign out anyone who is no longer an active staff member, e.g. an
     * account deactivated while it still had an open session.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && (! $user->isStaff() || ! $user->is_active)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'phone' => 'আপনার অ্যাকাউন্টটি এখন সক্রিয় নেই।',
            ]);
        }

        return $next($request);
    }
}
