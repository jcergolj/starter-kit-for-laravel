<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()->with('client')->orderBy('name')->paginate(15);
        
        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $project->load('client', 'timeEntries');
        
        return view('projects.show', compact('project'));
    }

    public function create(Request $request)
    {
        $clients = Client::query()->orderBy('name')->get();
        
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        // Handle quick create from search input (simplified for projects - just name)
        if ($request->has('quick_create') && $request->wantsJson()) {
            try {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                ]);

                $project = Project::query()->create([
                    'name' => $request->name,
                ]);

                activity()
                    ->performedOn($project)
                    ->log('Project created via quick create');

                return response()->json([
                    'success' => true,
                    'id' => $project->id,
                    'name' => $project->name,
                    'message' => 'Project created successfully!'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'redirect_url' => route('projects.create'),
                    'errors' => $e->errors()
                ], 422);
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $hourlyRateData = null;
        if ($request->hourly_rate && $request->currency) {
            $hourlyRateData = [
                'amount' => (float) $request->hourly_rate,
                'currency' => strtoupper($request->currency),
            ];
        }

        $project = Project::query()->create([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'hourly_rate' => $hourlyRateData ? json_encode($hourlyRateData) : null,
        ]);

        activity()
            ->performedOn($project)
            ->log('Project created');

        if ($request->header('Turbo-Frame')) {
            return turbo_stream([
                turbo_stream()->replace($request->header('Turbo-Frame'), ''),
                turbo_stream()->append('flash-messages', view('partials.flash', ['message' => 'Project created successfully!', 'type' => 'success']))
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        $clients = Client::query()->orderBy('name')->get();
        
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $hourlyRateData = null;
        if ($request->hourly_rate && $request->currency) {
            $hourlyRateData = [
                'amount' => (float) $request->hourly_rate,
                'currency' => strtoupper($request->currency),
            ];
        }

        $project->update([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'hourly_rate' => $hourlyRateData ? json_encode($hourlyRateData) : null,
        ]);

        activity()
            ->performedOn($project)
            ->log('Project updated');

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        activity()
            ->performedOn($project)
            ->log('Project deleted');

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
