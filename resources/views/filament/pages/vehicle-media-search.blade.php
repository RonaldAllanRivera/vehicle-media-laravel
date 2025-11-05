<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            {{ $this->form }}
        </div>

        @if ($result)
            @if(($result['status'] ?? null) === 'error')
                <div class="text-sm text-red-600">
                    <strong>Error</strong>:
                    {{ $result['error']['message'] ?? 'Unknown error' }}
                    @if(isset($result['error']['code']))
                        (Code: {{ $result['error']['code'] }})
                    @endif
                </div>
            @else
                <form method="POST" action="{{ route('media.bulk-download') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="year" value="{{ $result['data']['year'] ?? '' }}" />
                    <input type="hidden" name="make" value="{{ $result['data']['make'] ?? '' }}" />
                    <input type="hidden" name="model" value="{{ $result['data']['model'] ?? '' }}" />
                    <input type="hidden" name="trim" value="{{ $result['data']['trim'] ?? '' }}" />

                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm text-gray-700">
                            Showing results for
                            <span class="font-medium">{{ $result['data']['year'] ?? '' }} {{ $result['data']['make'] ?? '' }} {{ $result['data']['model'] ?? '' }} — {{ $result['data']['trim'] ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="fi-btn fi-color-primary fi-btn-size-md">
                                Bulk Download Selected
                            </button>
                        </div>
                    </div>
                    <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                        @foreach ($this->getGridCards() as $card)
                            <div class="group bg-white rounded-lg border shadow-sm overflow-hidden flex flex-col">
                                <div class="relative">
                                    <div class="w-full aspect-square bg-gray-50">
                                        <img src="{{ $card['url'] }}" alt="{{ trim(($card['year'] ?? '').' '.($card['make'] ?? '').' '.($card['model'] ?? '').' '.($card['trim'] ?? '').' - '.($card['category'] ?? '')) }}" loading="lazy" decoding="async" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="absolute top-2 left-2 bg-white/90 rounded px-2 py-0.5 text-[10px] uppercase tracking-wide">{{ $card['category'] }}</div>
                                </div>
                                <div class="p-3 flex-1 flex flex-col gap-2">
                                    <div class="text-xs text-gray-600 leading-tight">
                                        <div><span class="font-semibold">Year:</span> {{ $card['year'] }}</div>
                                        <div><span class="font-semibold">Make:</span> {{ $card['make'] }}</div>
                                        <div><span class="font-semibold">Model:</span> {{ $card['model'] }}</div>
                                        <div><span class="font-semibold">Trim:</span> {{ $card['trim'] }}</div>
                                    </div>
                                    <div class="mt-auto flex items-center justify-between gap-2">
                                        @if(($card['category'] ?? '') !== 'sample')
                                            <label class="inline-flex items-center gap-2 text-xs">
                                                <input type="checkbox" name="urls[]" value="{{ $card['url'] }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                                Select
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ $card['url'] }}" target="_blank" class="fi-btn fi-color-gray fi-btn-size-xs">View</a>
                                                <a href="{{ route('media.download', ['url' => $card['url']]) }}" class="fi-btn fi-color-primary fi-btn-size-xs">Download</a>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Sample</span>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ $card['url'] }}" target="_blank" class="fi-btn fi-color-gray fi-btn-size-xs">View</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            @endif
        @endif
    </div>
</x-filament-panels::page>

