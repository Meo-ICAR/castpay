<x-filament-panels::page>
    <div class="bg-white dark:bg-[#030712] -mx-8 -mt-8 p-8 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-20">
                <h2 class="text-sm font-semibold tracking-wide uppercase text-primary-500 mb-4">Pricing</h2>
                <p class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-6xl mb-6">
                    More power, when you need it
                </p>
                <p class="text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                    Choose the perfect plan for your professional casting career.
                </p>
            </div>

            <!-- Pricing Sections -->
            <div class="space-y-24">
                <!-- Grouping logic if needed, otherwise just list all -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-10 border-b border-gray-200 dark:border-gray-800 pb-4">
                        Available Services
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        @foreach($this->getServices() as $service)
                            <div class="relative flex flex-col rounded-2xl bg-white dark:bg-[#0B0F1A] p-8 shadow-sm border border-gray-200 dark:border-gray-800 transition-all hover:border-primary-500/50 hover:shadow-2xl hover:shadow-primary-500/10">
                                @if($service->requiredRole)
                                    <div class="absolute -top-4 left-8">
                                        <span class="inline-flex items-center rounded-full bg-primary-500 px-3 py-1 text-xs font-bold text-white uppercase tracking-wider">
                                            {{ $service->requiredRole->name }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-8">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $service->name }}</h3>
                                    <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400 min-h-[3rem]">
                                        {{ $service->description }}
                                    </p>
                                </div>

                                <div class="mb-10 flex-grow">
                                    @forelse($service->prices as $price)
                                        <div class="mb-6 last:mb-0">
                                            <div class="flex items-baseline text-gray-900 dark:text-white">
                                                <span class="text-4xl font-extrabold tracking-tight">
                                                    {{ \Illuminate\Support\Number::currency($price->amount / 100, $price->currency) }}
                                                </span>
                                                @if($price->type === 'recurring')
                                                    <span class="ml-1 text-xl font-semibold text-gray-500 dark:text-gray-500">/{{ $price->interval }}</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm font-medium text-primary-500 dark:text-primary-400">
                                                {{ $price->name ?: 'Standard Plan' }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-500 italic">Contact us for custom pricing</p>
                                    @endforelse
                                </div>

                                <ul role="list" class="mb-10 space-y-4 text-sm text-gray-600 dark:text-gray-400">
                                    <li class="flex items-start">
                                        <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 shrink-0 mr-3" />
                                        <span>Full context awareness</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 shrink-0 mr-3" />
                                        <span>Unlimited priority access</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 shrink-0 mr-3" />
                                        <span>Professional profile listing</span>
                                    </li>
                                </ul>

                                <a href="#" 
                                   class="mt-auto block w-full rounded-xl bg-primary-600 px-6 py-4 text-center text-sm font-bold text-white shadow-lg shadow-primary-500/20 hover:bg-primary-500 transition-all transform active:scale-[0.98]">
                                    Get Started
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="mt-32 border-t border-gray-200 dark:border-gray-800 pt-16">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
                    <div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Everything you need.</h3>
                        <p class="text-lg text-gray-500 dark:text-gray-400">
                            No complex tiers. No locked features. Just the power you need to scale your career in the casting industry.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 lg:justify-end">
                        <a href="#" class="inline-flex items-center justify-center rounded-xl bg-gray-900 dark:bg-white px-8 py-4 text-sm font-bold text-white dark:text-black hover:opacity-90 transition-all">
                            Book a demo
                        </a>
                        <a href="#" class="inline-flex items-center justify-center rounded-xl bg-transparent border-2 border-gray-200 dark:border-gray-800 px-8 py-4 text-sm font-bold text-gray-900 dark:text-white hover:border-primary-500 transition-all">
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
