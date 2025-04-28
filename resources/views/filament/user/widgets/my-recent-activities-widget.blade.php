<x-filament::section>
    <div class="space-y-6">
        <h2 class="text-xl font-bold tracking-tight">Recent Activities</h2>

        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @forelse($activities as $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex items-start space-x-3">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400">
                                    <x-dynamic-component :component="'heroicon-o-' . Str::after($activity['icon'], 'heroicon-o-')" class="h-5 w-5" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div>
                                        <div class="text-sm">
                                            <a href="{{ $activity['url'] }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                                                {{ $activity['title'] }}
                                            </a>
                                        </div>
                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $activity['description'] }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-clock class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400 dark:text-gray-500" />
                                            <span>{{ $activity['date']->diffForHumans() }}</span>
                                        </div>

                                        @if(isset($activity['status']))
                                            <div class="flex items-center text-sm">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                    @if($activity['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                    @elseif($activity['status'] === 'approved' || $activity['status'] === 'accepted' || $activity['status'] === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                    @elseif($activity['status'] === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                    @elseif($activity['status'] === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                    @endif">
                                                    {{ ucfirst($activity['status']) }}
                                                </span>
                                            </div>
                                        @endif

                                        @if(isset($activity['appointment_date']))
                                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <x-heroicon-o-calendar class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400 dark:text-gray-500" />
                                                <span>{{ $activity['appointment_date']->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="text-center py-6">
                        <p class="text-gray-500 dark:text-gray-400">No recent activities found.</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
</x-filament::section>
