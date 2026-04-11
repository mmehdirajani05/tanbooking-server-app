<?php

namespace App\Http\Controllers\Partner\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerEventController extends Controller
{
    public function index()
    {
        return view('partner.events.index');
    }

    public function create()
    {
        return view('partner.events.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement event creation
        return back()->with('success', 'Event created successfully (Coming Soon)');
    }
}