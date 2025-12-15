@if (filled($logo = config('filament.logo')))
    <img
        src="{{ $logo }}"
        alt="{{ config('filament.brand') }}"
        class="h-10"
    />
@elseif (filled($brand = config('filament.brand')))
    <div
        @class([
            'filament-brand text-xl font-bold leading-5 tracking-tight',
            'dark:text-white' => config('filament.dark_mode'),
        ])
    >
        {{ $brand }}
    </div>
@endif
