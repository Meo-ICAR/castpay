<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($this->getServices() as $service)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $service->name }}
                    @if($service->requiredRole)
                        <span class="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded-full ml-2">
                            {{ $service->requiredRole->name }} Only
                        </span>
                    @endif
                </x-slot>

                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $service->description }}</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($service->prices as $price)
                        <div class="border dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div class="text-sm font-medium text-gray-500">{{ $price->name ?: 'Standard' }}</div>
                            <div class="text-2xl font-bold">
                                {{ Number::currency($price->amount / 100, $price->currency) }}
                                @if($price->type === 'recurring')
                                    <span class="text-sm font-normal text-gray-500">/ {{ $price->interval }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-2">
                                Type: {{ ucfirst(str_replace('_', ' ', $price->type)) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">No prices defined for this service.</p>
                    @endforelse
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
