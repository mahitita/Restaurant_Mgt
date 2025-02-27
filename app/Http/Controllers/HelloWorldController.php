<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HelloWorldController extends Controller
{
    public function index()
    {
        return Inertia::render('HelloWorld');
    }
}
