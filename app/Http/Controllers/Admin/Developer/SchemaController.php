<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Support\DatabaseSchema;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Live ER diagram of the database (overall, or one table with its neighbours).
 */
class SchemaController extends Controller
{
    public function show(Request $request): View
    {
        $includeSystem = $request->boolean('system');
        $tables = DatabaseSchema::tables($includeSystem);
        $focus = $request->string('table')->toString() ?: null;

        if ($focus !== null && ! in_array($focus, array_column($tables, 'name'), true)) {
            $focus = null;
        }

        return view('admin.developer.schema', [
            'tables' => $tables,
            'focus' => $focus,
            'includeSystem' => $includeSystem,
            'diagram' => DatabaseSchema::mermaid($tables, $focus),
        ]);
    }
}
