<x-layouts.app title="Client Details">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $client->name }}</h1>
                <p class="text-gray-600">Client details and projects</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline">
                    Edit Client
                </a>
                <a href="{{ route('clients.index') }}" class="btn btn-ghost">
                    Back to Clients
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Client Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Client Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-900">{{ $client->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Default Hourly Rate</label>
                            @if($client->hourly_rate)
                                <p class="text-gray-900">{{ $client->hourly_rate->currency }} {{ number_format($client->hourly_rate->amount, 2) }}</p>
                            @else
                                <p class="text-gray-500">Not set</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created</label>
                            <p class="text-gray-900">{{ $client->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Projects</h2>
                            <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                                Add Project
                            </a>
                        </div>
                    </div>
                    
                    @if($client->projects->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($client->projects as $project)
                                <div class="px-6 py-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900">{{ $project->name }}</h3>
                                            @if($project->hourly_rate)
                                                <p class="text-sm text-gray-500">{{ $project->hourly_rate->currency }} {{ number_format($project->hourly_rate->amount, 2) }}/hour</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                View
                                            </a>
                                            <a href="{{ route('projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
                            <p class="text-gray-500 mb-4">Get started by creating a project for this client.</p>
                            <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                                Add First Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>