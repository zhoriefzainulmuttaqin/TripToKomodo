<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

class ToolsController
{
    public function index(): View
    {
        return view('admin.tools.index');
    }
}
