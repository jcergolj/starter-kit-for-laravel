<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeTrackingController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $runningTimer = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->first();

        if ($runningTimer) {
            return response()->json(['error' => 'A timer is already running.'], 400);
        }

        $timeEntry = TimeEntry::query()->create([
            'user_id' => Auth::id(),
            'client_id' => $request->client_id,
            'project_id' => $request->project_id,
            'started_at' => now(),
            'notes' => $request->notes,
        ]);

        activity()
            ->performedOn($timeEntry)
            ->log('Timer started');

        return response()->json([
            'success' => true,
            'time_entry' => $timeEntry->load(['client', 'project']),
        ]);
    }

    public function stop(Request $request)
    {
        $runningTimer = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->first();

        if (!$runningTimer) {
            return response()->json(['error' => 'No running timer found.'], 400);
        }

        $runningTimer->update([
            'ended_at' => now(),
        ]);

        activity()
            ->performedOn($runningTimer)
            ->log('Timer stopped');

        return response()->json([
            'success' => true,
            'time_entry' => $runningTimer->load(['client', 'project']),
            'duration' => $runningTimer->getDurationFormatted(),
        ]);
    }

    public function current()
    {
        $runningTimer = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->with(['client', 'project'])
            ->first();

        return response()->json([
            'running_timer' => $runningTimer,
        ]);
    }

    public function recent()
    {
        $entries = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->with(['client', 'project'])
            ->latest('started_at')
            ->limit(5)
            ->get();

        return response()->json([
            'recent_entries' => $entries,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $timeEntry = TimeEntry::query()->create([
            'user_id' => Auth::id(),
            'client_id' => $request->client_id,
            'project_id' => $request->project_id,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'notes' => $request->notes,
        ]);

        activity()
            ->performedOn($timeEntry)
            ->log('Manual time entry created');

        return response()->json([
            'success' => true,
            'time_entry' => $timeEntry->load(['client', 'project']),
        ]);
    }
}
