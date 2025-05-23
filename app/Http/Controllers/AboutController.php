<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AboutController extends Controller
{
    /**
     * Display the about page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('About');
    }
}
