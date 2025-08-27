<x-layouts.app :title="__('Profile & Settings')">
    <section class="w-full max-w-md mx-auto space-y-6">
        @unlesshotwirenative
            <x-text.heading size="xl">{{ __('Profile & Settings') }}</x-text.heading>
        @endunlesshotwirenative

        <x-menu>
            <x-menu.link icon="user" :href="route('settings.profile.edit')">{{ __('Edit profile') }}</x-menu.link>
            <x-menu.link icon="key" :href="route('settings.password.edit')">{{ __('Change password') }}</x-menu.link>
            <x-menu.link icon="trash" :href="route('settings.profile.delete')">{{ __('Delete profile') }}</x-menu.link>
        </x-menu>

        @php
            $themes = ['default', 'acid', 'autumn', 'bumblebee', 'dark', 'light'];
            $currentTheme = session('theme', 'default');
        @endphp

        <form action="{{ route('theme.update') }}" method="post" id="theme-form" data-controller="theme" data-theme-active-class="btn-active [&_svg]:visible!" data-action="submit->theme#updateFromSubmit">
            @csrf
            @method('PUT')

            <button type="submit" class="sr-only">{{ __('Update Theme') }}</button>

            <x-menu>
                <div class="dropdown dropdown-top w-full">
                    <div tabindex="0" role="button" class="flex items-center gap-3 px-4 py-3 text-left hover:bg-base-200 rounded-lg cursor-pointer w-full">
                        <x-heroicon-o-paint-brush class="size-5 text-base-content/70" />
                        <div class="flex-1">
                            <span>{{ __('Theme') }}</span>
                            <div class="text-xs text-base-content/60" data-theme-target="currentTheme">{{ $currentTheme }}</div>
                        </div>
                        <x-heroicon-o-chevron-up-down class="size-4 text-base-content/50" />
                    </div>

                    <ul tabindex="0" class="dropdown-content max-h-[50vh] overflow-y-auto bg-base-100 rounded-box z-1 w-52 p-2 shadow-2xl border border-base-300">
                        <li class="menu-title text-xs">{{ __('Theme') }}</li>
                        @foreach ($themes as $theme)
                        <li>
                            <button class="btn btn-ghost w-full flex gap-3 px-3 py-2 justify-start @if($currentTheme === $theme) btn-active @endif" data-theme-target="button" type="submit" name="theme" value="{{ $theme }}">
                                <div data-theme="{{ $theme }}" class="bg-base-100 grid shrink-0 grid-cols-2 gap-0.5 rounded-md p-1 shadow-sm border">
                                    <div class="bg-base-content size-1 rounded-full"></div>
                                    <div class="bg-primary size-1 rounded-full"></div>
                                    <div class="bg-secondary size-1 rounded-full"></div>
                                    <div class="bg-accent size-1 rounded-full"></div>
                                </div>

                                <div class="flex-1 truncate text-left">{{ $theme }}</div>

                                 @if($currentTheme === $theme)
                                    <x-heroicon-o-check class="size-4 text-success" />
                               @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </x-menu>
        </form>

        <x-menu>
            <x-menu.button icon="chevron-left" form="settings-logout" type="submit">{{ __('Logout') }}</x-menu>
        </x-menu>
    </section>

    <form action="{{ route('logout') }}" method="post" id="settings-logout" data-turbo-action="replace"
        data-action="submit->theme#clear">
        @csrf
    </form>
</x-layouts.app>
