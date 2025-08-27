<x-layouts.app title="Create Client">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Client</h1>
            <p class="text-gray-600 dark:text-gray-400">Add a new client to your system</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <turbo-frame id="create_client_form">
            <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <x-form.label for="name">Client Name</x-form.label>
                    <x-form.text-input 
                        id="name" 
                        name="name" 
                        required 
                        autofocus 
                        placeholder="Enter client name"
                        value="{{ old('name') }}"
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
                            value="{{ old('hourly_rate') }}"
                        />
                        <select name="currency" class="input data-error:input-error">
                            <option value="">Currency</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="CAD" {{ old('currency') === 'CAD' ? 'selected' : '' }}>CAD</option>
                            <option value="AUD" {{ old('currency') === 'AUD' ? 'selected' : '' }}>AUD</option>
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
                    <a href="{{ route('clients.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <x-form.button.primary type="submit">
                        Create Client
                    </x-form.button.primary>
                </div>
            </form>
            </turbo-frame>
        </div>
    </div>
</x-layouts.app>
