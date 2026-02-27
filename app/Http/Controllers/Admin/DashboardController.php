<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController
{
    public function index(): View
    {
        $destinationCount = 0;

        try {
            $destinationCount = DB::table('destinations')->count();
        } catch (\Throwable) {
            $destinationCount = 0;
        }

        return view('admin.dashboard', [
            'destinationCount' => $destinationCount,
        ]);
    }
}
