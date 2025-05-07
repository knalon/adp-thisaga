<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Contact');
    }
}
