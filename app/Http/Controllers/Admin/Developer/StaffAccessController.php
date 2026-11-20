<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StaffNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Support tools for any staff account: view the panel as them
 * ("switch profile") to reproduce a problem, or reset a forgotten password.
 */
class StaffAccessController extends Controller
{
    /**
     * Session key holding the developer's own user id while switched.
     */
    public const ImpersonatorKey = 'impersonator_id';

    private const GENERATED_PASSWORD_LENGTH = 10;

    public function index(Request $request): View
    {
        return view('admin.developer.staff', [
            'users' => User::query()->where('type', User::TypeStaff)->with('roles')->orderBy('name')->get(),
            'canSwitchProfiles' => $request->user()->isDeveloper(),
        ]);
    }

    /**
     * Only the developer role may switch profiles — not super admins, even
     * though they can open the other developer tools. It is a support tool
     * for reproducing problems, not part of running the business.
     */
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        $developer = $request->user();

        abort_unless($developer->isDeveloper(), 403, 'প্রোফাইল সুইচ শুধু ডেভেলপার অ্যাকাউন্ট থেকে করা যায়।');

        if ($user->is($developer) || ! $user->is_active || ! $user->isStaff()) {
            return back()->withErrors(['general' => 'এই প্রোফাইলে সুইচ করা যাবে না।']);
        }

        $request->session()->put(self::ImpersonatorKey, $developer->id);
        Auth::login($user);
        Log::info('Developer switched profile', ['developer_id' => $developer->id, 'user_id' => $user->id]);

        return to_route('admin.dashboard')->with('status', "আপনি এখন {$user->name}-এর প্রোফাইল দেখছেন।");
    }

    /**
     * Available to the switched session (the target may not be a developer).
     */
    public function stopImpersonating(Request $request): RedirectResponse
    {
        $developerId = $request->session()->pull(self::ImpersonatorKey);
        $developer = $developerId ? User::find($developerId) : null;

        abort_unless($developer !== null && $developer->isDeveloper(), 403);

        Auth::login($developer);

        return to_route('admin.developer.staff')->with('status', 'নিজের প্রোফাইলে ফিরে এসেছেন।');
    }

    /**
     * Generate a new password, show it once, and notify the staff member.
     */
    public function resetPassword(Request $request, User $user, StaffNotifier $notifier): RedirectResponse
    {
        $password = Str::password(self::GENERATED_PASSWORD_LENGTH, symbols: false);

        $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
        $notifier->passwordReset($user, $request->user());
        Log::info('Developer reset a staff password', ['developer_id' => $request->user()->id, 'user_id' => $user->id]);

        return back()
            ->with('status', "{$user->name}-এর নতুন পাসওয়ার্ড তৈরি হয়েছে। এখনই কপি করে তাঁকে জানান — এটি আর দেখানো হবে না।")
            ->with('generated_password', ['user' => $user->name, 'phone' => $user->phone, 'password' => $password]);
    }
}
