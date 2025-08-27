<turbo-frame id="available_clients">
    <div class="col-md-12 col-sm-12 border">
        @if (count($availableClients) > 0)
            @foreach ($availableClients as $client)
                <div class="row py-2" id="available-client-{{ $client->id }}">
                    <div class="col-md-2">{{ $client->name }}</div>
                    <div class="col-md-2">
                        <a data-turbo-frame="false" data-turbo-frame="clients" data-selector="clients" data-turbo-method="post"
                            href="{{ route('teams-members.store', ['member_id' => $client->id]) }}">
                            @lang('teams._token.add')
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row py-2 pl-2">
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
            </div>
        @endif
    </div>
</turbo-frame>
