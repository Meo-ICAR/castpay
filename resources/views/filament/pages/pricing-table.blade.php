<div class="grid grid-cols-1 gap-6 py-8 md:grid-cols-3">
    @foreach ($plans as $plan)
        <div @class([
            'relative flex flex-col p-6 rounded-2xl border bg-white dark:bg-gray-900',
            'ring-2 ring-primary-500 scale-105 z-10' => $plan['featured'],
            'border-gray-200 dark:border-gray-700' => !$plan['featured'],
        ])>
            @if ($plan['featured'])
                <span
                    class="bg-primary-500 absolute right-0 top-0 -translate-y-1/2 translate-x-1/2 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                    Popolare
                </span>
            @endif

            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $plan['name'] }}</h3>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ $plan['description'] }}</p>
                <p class="mt-8">
                    <span
                        class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">€{{ $plan['price'] }}</span>
                    <span class="text-base font-medium text-gray-500">/mese</span>
                </p>
            </div>

            <ul class="mb-8 flex-1 space-y-4">
                @foreach ($plan['features'] as $feature)
                    <li class="flex items-start">
                        <svg class="text-success-500 h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-600 dark:text-gray-300">{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>

            <x-filament::button :color="$plan['featured'] ? 'primary' : 'gray'" tag="a" href="#" size="lg" class="w-full">
                {{ $plan['button_text'] }}
            </x-filament::button>
        </div>
    @endforeach
</div>
