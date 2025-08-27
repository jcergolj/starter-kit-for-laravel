<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if($request->wantsTurboStream()) {
            if ($request->search === null) {
                return turbo_stream()->replace('available_clients')
                    ->view('components.available-clients', ['availableClients' => []]);
            }

            $tokens = Client::query()
                ->where('name', 'like', '%' . $request->search . '%')
                ->limit(5)
                ->get();

            return view('clients.search-results', compact('clients', 'query'));
        }

        $clients = Client::query()->orderBy('name')->paginate(15);
        
        return view('clients.index', compact('clients'));
    }

    public function show(Client $client)
    {
        $client->load('projects');
        
        return view('clients.show', compact('client'));
    }

    public function create(Request $request)
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        if ($request->has('quick_create') && $request->wantsJson()) {
            try {
                $request->validate([
                    'name' => ['required', 'string', 'max:255', 'unique:clients,name'],
                    'hourly_rate' => ['required', 'numeric', 'min:0'],
                    'currency' => ['required', 'string', 'size:3'],
                ]);

                $hourlyRateData = [
                    'amount' => (float) $request->hourly_rate,
                    'currency' => strtoupper($request->currency),
                ];

                $client = Client::query()->create([
                    'name' => $request->name,
                    'hourly_rate' => json_encode($hourlyRateData),
                ]);

                activity()
                    ->performedOn($client)
                    ->log('Client created via quick create');

                return response()->json([
                    'success' => true,
                    'id' => $client->id,
                    'name' => $client->name,
                    'message' => 'Client created successfully!'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'redirect_url' => route('clients.create'),
                    'errors' => $e->errors()
                ], 422);
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:clients,name'],
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

        $client = Client::query()->create([
            'name' => $request->name,
            'hourly_rate' => $hourlyRateData ? json_encode($hourlyRateData) : null,
        ]);

        activity()
            ->performedOn($client)
            ->log('Client created');

        if ($request->header('Turbo-Frame')) {
            return turbo_stream([
                turbo_stream()->replace($request->header('Turbo-Frame'), ''),
                turbo_stream()->append('flash-messages', view('partials.flash', ['message' => 'Client created successfully!', 'type' => 'success']))
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:clients,name,' . $client->id],
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

        $client->update([
            'name' => $request->name,
            'hourly_rate' => $hourlyRateData ? json_encode($hourlyRateData) : null,
        ]);

        activity()
            ->performedOn($client)
            ->log('Client updated');

        return redirect()->route('clients.show', $client)->with('success', 'Client updated successfully!');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        activity()
            ->performedOn($client)
            ->log('Client deleted');

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');
    }
}
