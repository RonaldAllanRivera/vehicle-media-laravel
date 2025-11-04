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
                @php($images = $result['data']['images'] ?? [])

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

                    <div class="grid grid-cols-1 gap-8">
                        @foreach (['exterior' => 'Exterior Images', 'interior' => 'Interior Images', 'colors' => 'Colors'] as $key => $label)
                            @if(!empty($images[$key]))
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-base font-semibold">{{ $label }}</h3>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                        @foreach ($images[$key] as $i => $url)
                                            <div class="group bg-white rounded-lg border shadow-sm overflow-hidden flex flex-col">
                                                <div class="relative">
                                                    <img src="{{ $url }}" alt="{{ ($result['data']['year'] ?? '') . ' ' . ($result['data']['make'] ?? '') . ' ' . ($result['data']['model'] ?? '') . ' ' . ($result['data']['trim'] ?? '') . ' - ' . $key }}" loading="lazy" decoding="async" class="w-full aspect-video object-cover" />
                                                    <div class="absolute inset-0 ring-0 group-hover:ring-2 ring-primary-500 transition"></div>
                                                    <div class="absolute top-2 left-2 bg-white/90 rounded px-2 py-0.5 text-[10px] uppercase tracking-wide">{{ $key }}</div>
                                                </div>
                                                <div class="p-3 flex-1 flex flex-col gap-2">
                                                    <div class="text-xs text-gray-600 leading-tight">
                                                        <div><span class="font-semibold">Year:</span> {{ $result['data']['year'] ?? '' }}</div>
                                                        <div><span class="font-semibold">Make:</span> {{ $result['data']['make'] ?? '' }}</div>
                                                        <div><span class="font-semibold">Model:</span> {{ $result['data']['model'] ?? '' }}</div>
                                                        <div><span class="font-semibold">Trim:</span> {{ $result['data']['trim'] ?? '' }}</div>
                                                    </div>
                                                    <div class="mt-auto flex items-center justify-between gap-2">
                                                        <label class="inline-flex items-center gap-2 text-xs">
                                                            <input type="checkbox" name="urls[]" value="{{ $url }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                                            Select
                                                        </label>
                                                        <div class="flex items-center gap-2">
                                                            <a href="{{ $url }}" target="_blank" class="fi-btn fi-color-gray fi-btn-size-xs">View</a>
                                                            <a href="{{ route('media.download', ['url' => $url]) }}" class="fi-btn fi-color-primary fi-btn-size-xs">Download</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </form>
            @endif
        @endif
    </div>
</x-filament-panels::page>
