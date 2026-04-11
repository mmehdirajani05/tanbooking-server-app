<?php

namespace App\Http\Controllers\Partner\Tourism;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerTourismController extends Controller
{
    public function index()
    {
        return view('partner.tourism.index');
    }

    public function create()
    {
        return view('partner.tourism.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement package creation
        return back()->with('success', 'Package created successfully (Coming Soon)');
    }
}