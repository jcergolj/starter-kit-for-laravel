<x-layouts.app title="Project Details">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
                <p class="text-gray-600">Project details and time entries</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline">
                    Edit Project
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-ghost">
                    Back to Projects
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Project Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Project Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-900">{{ $project->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Client</label>
                            @if($project->client)
                                <p class="text-gray-900">
                                    <a href="{{ route('clients.show', $project->client) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $project->client->name }}
                                    </a>
                                </p>
                            @else
                                <p class="text-gray-500">No client assigned</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Hourly Rate</label>
                            @if($project->hourly_rate)
                                <p class="text-gray-900">{{ $project->hourly_rate->currency }} {{ number_format($project->hourly_rate->amount, 2) }}</p>
                            @elseif($project->client && $project->client->hourly_rate)
                                <p class="text-gray-500">{{ $project->client->hourly_rate->currency }} {{ number_format($project->client->hourly_rate->amount, 2) }} (from client)</p>
                            @else
                                <p class="text-gray-500">Not set</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created</label>
                            <p class="text-gray-900">{{ $project->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Entries -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Time Entries</h2>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                Start Timer
                            </a>
                        </div>
                    </div>
                    
                    @if($project->timeEntries->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($project->timeEntries->take(10) as $entry)
                                <div class="px-6 py-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if($entry->isRunning())
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                    <span class="text-sm font-medium text-green-600">Running</span>
                                                </div>
                                            @endif
                                            
                                            @if($entry->notes)
                                                <p class="text-sm text-gray-900 font-medium">{{ $entry->notes }}</p>
                                            @else
                                                <p class="text-sm text-gray-500 italic">No notes</p>
                                            @endif
                                            
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $entry->started_at->format('M j, Y g:i A') }}
                                                @if($entry->ended_at)
                                                    - {{ $entry->ended_at->format('g:i A') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($entry->getDurationInSeconds())
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ gmdate('H:i:s', $entry->getDurationInSeconds()) }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">--:--:--</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No time entries yet</h3>
                            <p class="text-gray-500 mb-4">Start tracking time for this project.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                Start Timer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>