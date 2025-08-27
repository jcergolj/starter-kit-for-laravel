<x-layouts.app title="Edit Client">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Client</h1>
            <p class="text-gray-600">Update client information</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <x-form.label for="name">Client Name</x-form.label>
                    <x-form.text-input 
                        id="name" 
                        name="name" 
                        required 
                        autofocus 
                        placeholder="Enter client name"
                        value="{{ old('name', $client->name) }}"
                    />
                    @if($errors->has('name'))
                        <x-form.error :message="$errors->first('name')" />
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
                            value="{{ old('hourly_rate', $client->hourly_rate?->amount) }}"
                        />
                        <select name="currency" class="input data-error:input-error">
                            <option value="">Currency</option>
                            <option value="USD" {{ old('currency', $client->hourly_rate?->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency', $client->hourly_rate?->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ old('currency', $client->hourly_rate?->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="CAD" {{ old('currency', $client->hourly_rate?->currency) === 'CAD' ? 'selected' : '' }}>CAD</option>
                            <option value="AUD" {{ old('currency', $client->hourly_rate?->currency) === 'AUD' ? 'selected' : '' }}>AUD</option>
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
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <x-form.button.primary type="submit">
                        Update Client
                    </x-form.button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>