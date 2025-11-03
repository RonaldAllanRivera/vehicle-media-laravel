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
                <div class="grid grid-cols-1 gap-6">
                    @php($images = $result['data']['images'] ?? [])

                    @foreach (['exterior' => 'Exterior Images', 'interior' => 'Interior Images', 'colors' => 'Colors'] as $key => $label)
                        @if(!empty($images[$key]))
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold">{{ $label }}</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                    @foreach ($images[$key] as $url)
                                        <a href="{{ $url }}" target="_blank" class="block">
                                            <img src="{{ $url }}" alt="{{ $key }}" class="w-full aspect-video object-cover rounded border" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
