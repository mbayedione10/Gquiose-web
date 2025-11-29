
<x-filament::page>
    <!-- En-tête du Dashboard -->
    <div class="mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2 text-white">
                    👋 Bienvenue, {{ auth()->user()->name }}
                </h1>
                <p class="text-white/90 text-lg">
                    Tableau de bord administrateur - {{ now()->format('d M Y') }}
                </p>
            </div>
            <div class="text-right text-white">
                <div class="text-5xl font-bold">{{ now()->format('H:i') }}</div>
                <div class="text-white/80">{{ now()->translatedFormat('l') }}</div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\QuickActionsWidget::class)
    </div>

    <!-- Activité des 7 derniers jours (statistiques) -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-6">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
            @livewire(\App\Filament\Widgets\ActivityChartWidget::class)
        </div>
    </div>

    <!-- Alertes Récentes et Notifications récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @livewire(\App\Filament\Widgets\LastAlert::class)
        @livewire(\App\Filament\Widgets\RecentActivityWidget::class)
    </div>
</x-filament::page>
