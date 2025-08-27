<x-layouts.app title="Edit Project">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Project</h1>
            <p class="text-gray-600">Update project information</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <x-form.label for="name">Project Name</x-form.label>
                    <x-form.text-input 
                        id="name" 
                        name="name" 
                        required 
                        autofocus 
                        placeholder="Enter project name"
                        value="{{ old('name', $project->name) }}"
                    />
                    @if($errors->has('name'))
                        <x-form.error :message="$errors->first('name')" />
                    @endif
                </div>
                
                <div>
                    <x-form.label for="client_id">Client (Optional)</x-form.label>
                    <select name="client_id" id="client_id" class="w-full input data-error:input-error">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('client_id'))
                        <x-form.error :message="$errors->first('client_id')" />
                    @endif
                </div>
                
                <div>
                    <x-form.label>Default Hourly Rate (Optional)</x-form.label>
                    <div class="flex gap-2">
                        <x-form.text-input 
                            name="hourly_rate" 
                            type="number"
                            step="0.01"
                            min="0"
                            class="flex-1"
                            placeholder="0.00"
                            value="{{ old('hourly_rate', $project->hourly_rate?->amount) }}"
                        />
                        <select name="currency" class="input data-error:input-error">
                            <option value="">Currency</option>
                            <option value="USD" {{ old('currency', $project->hourly_rate?->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency', $project->hourly_rate?->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ old('currency', $project->hourly_rate?->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="CAD" {{ old('currency', $project->hourly_rate?->currency) === 'CAD' ? 'selected' : '' }}>CAD</option>
                            <option value="AUD" {{ old('currency', $project->hourly_rate?->currency) === 'AUD' ? 'selected' : '' }}>AUD</option>
                        </select>
                    </div>
                    @if($errors->has('hourly_rate'))
                        <x-form.error :message="$errors->first('hourly_rate')" />
                    @endif
                    @if($errors->has('currency'))
                        <x-form.error :message="$errors->first('currency')" />
                    @endif
                </div>
                
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <x-form.button.primary type="submit">
                        Update Project
                    </x-form.button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>