<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Port;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $ports = Port::with('country')->get();
        return view('admin.dashboard', compact('users', 'ports'));
    }
}
