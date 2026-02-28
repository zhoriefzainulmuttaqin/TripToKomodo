<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function rentalMobil(): View
    {
        return view('pages.rental-mobil');
    }

    public function blog(): View
    {
        return view('pages.blog');
    }
}
