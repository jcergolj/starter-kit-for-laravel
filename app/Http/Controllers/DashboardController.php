<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $runningTimer = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->with(['client', 'project'])
            ->first();

        $recentEntries = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->with(['client', 'project'])
            ->latest('started_at')
            ->limit(5)
            ->get();

        $clients = Client::query()->orderBy('name')->get();
        $projects = Project::query()->with('client')->orderBy('name')->get();

        return view('dashboard', compact('runningTimer', 'recentEntries', 'clients', 'projects'));
    }
}
