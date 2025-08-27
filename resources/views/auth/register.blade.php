<x-layouts.auth :title="__('Setup Time Tracker')">
    <div class="flex flex-col items-center justify-center min-h-screen py-6 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h1 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Welcome to Time Tracker
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Let's set up your time tracking application
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
                    @csrf

                    <!-- User Information -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                            User Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <x-form.label for="name">{{ __('Name') }}</x-form.label>
                                <x-form.text-input 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="name" 
                                />
                                @if($errors->has('name'))
                                    <x-form.error :message="$errors->first('name')" />
                                @endif
                            </div>

                            <div>
                                <x-form.label for="email">{{ __('Email') }}</x-form.label>
                                <x-form.text-input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="username" 
                                />
                                @if($errors->has('email'))
                                    <x-form.error :message="$errors->first('email')" />
                                @endif
                            </div>

                            <div>
                                <x-form.label for="password">{{ __('Password') }}</x-form.label>
                                <x-form.password-input 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password" 
                                />
                                @if($errors->has('password'))
                                    <x-form.error :message="$errors->first('password')" />
                                @endif
                            </div>

                            <div>
                                <x-form.label for="password_confirmation">{{ __('Confirm Password') }}</x-form.label>
                                <x-form.password-input 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password" 
                                />
                                @if($errors->has('password_confirmation'))
                                    <x-form.error :message="$errors->first('password_confirmation')" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Default Settings -->
                    <div class="pt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                            Default Settings
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <x-form.label for="default_currency">{{ __('Default Currency') }}</x-form.label>
                                <select 
                                    id="default_currency" 
                                    name="default_currency" 
                                    class="w-full input data-error:input-error"
                                    required
                                >
                                    <option value="">Select Currency</option>
                                    <option value="USD" {{ old('default_currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('default_currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('default_currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="CAD" {{ old('default_currency') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                    <option value="AUD" {{ old('default_currency') === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                </select>
                                @if($errors->has('default_currency'))
                                    <x-form.error :message="$errors->first('default_currency')" />
                                @endif
                            </div>

                            <div>
                                <x-form.label for="default_hourly_rate">{{ __('Default Hourly Rate') }}</x-form.label>
                                <x-form.text-input 
                                    id="default_hourly_rate" 
                                    name="default_hourly_rate" 
                                    type="number" 
                                    step="0.01"
                                    min="0"
                                    value="{{ old('default_hourly_rate') }}" 
                                    required 
                                />
                                @if($errors->has('default_hourly_rate'))
                                    <x-form.error :message="$errors->first('default_hourly_rate')" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-form.button.primary class="w-full justify-center">
                            {{ __('Complete Setup') }}
                        </x-form.button.primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>