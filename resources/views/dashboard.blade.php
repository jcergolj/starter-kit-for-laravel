<x-layouts.app :title="__('Time Tracker')">
    <!-- Flash messages container -->
    <div id="flash-messages"></div>
    
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Row 1: Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Today</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $recentEntries->where('started_at', '>=', today())->sum(function($entry) {
                        return $entry->getDurationInSeconds() ?? 0;
                    }) ? gmdate('H:i:s', $recentEntries->where('started_at', '>=', today())->sum(function($entry) {
                        return $entry->getDurationInSeconds() ?? 0;
                    })) : '00:00:00' }}
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">This Week</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $recentEntries->where('started_at', '>=', now()->startOfWeek())->sum(function($entry) {
                        return $entry->getDurationInSeconds() ?? 0;
                    }) ? gmdate('H:i:s', $recentEntries->where('started_at', '>=', now()->startOfWeek())->sum(function($entry) {
                        return $entry->getDurationInSeconds() ?? 0;
                    })) : '00:00:00' }}
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Entries</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $recentEntries->count() }}</p>
            </div>
        </div>

        <!-- Row 2: Timer Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
            <div class="text-center">
                <!-- Timer Display -->
                <div id="timer-display" class="text-6xl font-mono font-bold text-gray-900 dark:text-white mb-4">
                    00:00:00
                </div>
                
                <!-- Current Project Info -->
                <div id="current-project" class="text-lg text-gray-600 dark:text-gray-400 mb-6 min-h-[1.5rem]">
                    @if($runningTimer)
                        <span class="text-green-600 dark:text-green-400">●</span>
                        {{ $runningTimer->client?->name ? $runningTimer->client->name . ' - ' : '' }}
                        {{ $runningTimer->project?->name ?? 'No Project' }}
                    @else
                        Not tracking time
                    @endif
                </div>

                <!-- Timer Controls -->
                <div class="flex justify-center gap-4">
                    <button 
                        id="start-timer-btn" 
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors{{ $runningTimer ? ' hidden' : '' }}"
                    >
                        Start Timer
                    </button>
                    
                    <button 
                        id="stop-timer-btn" 
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors{{ !$runningTimer ? ' hidden' : '' }}"
                    >
                        Stop Timer
                    </button>
                </div>

                <!-- Quick Timer Form -->
                <div id="quick-timer-form" class="mt-6{{ $runningTimer ? ' hidden' : '' }}">
                    <form id="timer-form" class="flex flex-wrap justify-center gap-4">
                        @csrf
                        <div class="relative min-w-[200px]">
                            <div id="client-select-wrapper">
    <label for="client-search">Client</label>
    <input 
        type="text" 
        id="client-search" 
        name="search" 
        placeholder="Search clients..." 
        autocomplete="off"
        data-controller="client-select"
    >

    <div id="client-results"></div>
</div>
                        
                        <div class="relative min-w-[200px]">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Row 3: Recent Entries -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Entries</h2>
            </div>
            
            <div id="recent-entries" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentEntries as $entry)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    @if($entry->isRunning())
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Running</span>
                                    @endif
                                    
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $entry->client?->name ? $entry->client->name . ' - ' : '' }}
                                        {{ $entry->project?->name ?? 'No Project' }}
                                    </span>
                                </div>
                                
                                @if($entry->notes)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $entry->notes }}</p>
                                @endif
                                
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $entry->started_at->format('M j, Y g:i A') }}
                                    @if($entry->ended_at)
                                        - {{ $entry->ended_at->format('g:i A') }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                {{ $entry->getDurationFormatted() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No time entries yet. Start your first timer!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Manual Entry Button -->
        <div class="mt-6 text-center">
            <button 
                id="manual-entry-btn" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
            >
                Add Manual Entry
            </button>
        </div>
    </div>
</x-layouts.app>
