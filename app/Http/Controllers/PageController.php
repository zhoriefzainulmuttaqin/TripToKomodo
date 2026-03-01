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

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function about(): View
    {
        return view('pages.about');
    }


}
