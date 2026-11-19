<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramController extends Controller
{
    use HandlesImageInput;

    public function index(): View
    {
        $programs = Program::query()
            ->withCount('courses')
            ->orderBy('sort_order')
            ->get();

        return view('admin.programs.index', compact('programs'));
    }

    public function create(): View
    {
        return view('admin.programs.form', ['program' => new Program(['accent' => 'teal', 'status' => 'draft'])]);
    }

    public function store(SaveProgramRequest $request): RedirectResponse
    {
        $program = Program::create([
            ...$request->attributesForModel(),
            'image_url' => $this->resolveImageUrl($request, 'image'),
        ]);

        return to_route('admin.programs.index')->with('status', "“{$program->translate('name')}” তৈরি হয়েছে।");
    }

    public function edit(Program $program): View
    {
        return view('admin.programs.form', compact('program'));
    }

    public function update(SaveProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update([
            ...$request->attributesForModel(),
            'image_url' => $this->resolveImageUrl($request, 'image', $program->image_url),
        ]);

        return to_route('admin.programs.index')->with('status', "“{$program->translate('name')}” আপডেট হয়েছে।");
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->courses()->exists()) {
            return back()->withErrors(['general' => 'এই প্রোগ্রামে কোর্স আছে। আগে কোর্সগুলো সরান বা অন্য প্রোগ্রামে নিন।']);
        }

        $program->delete();

        return to_route('admin.programs.index')->with('status', 'প্রোগ্রামটি মুছে ফেলা হয়েছে।');
    }
}
