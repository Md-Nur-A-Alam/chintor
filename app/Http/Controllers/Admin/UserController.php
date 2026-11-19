<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class UserController extends Controller
{
    private const PROFILE_FIELDS = ['name', 'phone', 'email', 'office_id', 'designation', 'joined_on', 'date_of_birth', 'is_active'];

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $users = User::query()
            ->with('roles')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('office_id', 'like', "%{$search}%");
            }))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['is_active' => true]),
            'roles' => Role::orderBy('id')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            ...Arr::only($request->validated(), self::PROFILE_FIELDS),
            'password' => $request->validated('password'),
            'type' => User::TypeStaff,
        ]);

        $user->roles()->sync($request->validated('roles'));

        return to_route('admin.users.index')->with('status', "{$user->name}-এর অ্যাকাউন্ট তৈরি হয়েছে।");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('id')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->fill(Arr::only($request->validated(), self::PROFILE_FIELDS));

        if (filled($request->validated('password'))) {
            $user->password = $request->validated('password');
        }

        $user->save();
        $user->roles()->sync($request->validated('roles'));

        return to_route('admin.users.index')->with('status', "{$user->name}-এর তথ্য আপডেট হয়েছে।");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->can('users.manage'), 403);

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।']);
        }

        if ($user->isSuperAdmin() && ! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $user->delete();

        return to_route('admin.users.index')->with('status', "{$user->name}-এর অ্যাকাউন্ট মুছে ফেলা হয়েছে।");
    }
}
