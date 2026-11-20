<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * In-app notifications for every staff role (the bell in the admin header).
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(30),
        ]);
    }

    /**
     * Unread count and the latest few, polled by the header bell.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'unread' => $user->unreadNotifications()->count(),
            'latest' => $user->notifications()->limit(8)->get()->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? '',
                'body' => $notification->data['body'] ?? null,
                'icon' => $notification->data['icon'] ?? 'star',
                'read' => $notification->read_at !== null,
                'time' => $notification->created_at->diffForHumans(),
                'open_url' => route('admin.notifications.open', $notification->id),
            ]),
        ]);
    }

    /**
     * Mark one as read and go to what it is about.
     */
    public function open(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('admin.notifications.index'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'সব নোটিফিকেশন পড়া হয়েছে।');
    }
}
