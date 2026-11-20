<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Role-based user guide. Each role's guide is a Markdown file in
 * resources/docs/guides/{role}.md (plus common.md for everyone), so the
 * text can be improved without touching code. Admins and developers can
 * read every role's guide.
 */
class GuideController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('roles');

        $roles = $user->hasFullAccess()
            ? Role::orderBy('id')->get()
            : $user->roles;

        $guides = collect(['common' => 'সবার জন্য'])
            ->merge($roles->pluck('label', 'name'))
            ->filter(fn (string $label, string $name) => is_file($this->path($name)))
            ->map(fn (string $label, string $name) => [
                'label' => $label,
                'html' => Str::markdown((string) file_get_contents($this->path($name)), [
                    'html_input' => 'escape',
                    'allow_unsafe_links' => false,
                ]),
            ]);

        return view('admin.guide', [
            'guides' => $guides,
            'active' => $request->string('role')->toString() ?: ($user->roles->first()?->name ?? 'common'),
        ]);
    }

    private function path(string $guide): string
    {
        return resource_path('docs/guides/'.Str::slug($guide, '_').'.md');
    }
}
