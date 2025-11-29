
<x-filament::page>
    <!-- En-tête du Dashboard -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Vue d'ensemble de l'application</p>
        
    <div class="space-y-6">
        <!-- En-tête du Dashboard -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 text-black">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        👋 Bienvenue, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-white/90 text-lg">
                        Tableau de bord administrateur - {{ now()->format('d M Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-5xl font-bold">{{ now()->format('H:i') }}</div>
                    <div class="text-white/80">{{ now()->translatedFormat('l') }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Actions Rapides -->
    @livewire(\App\Filament\Widgets\QuickActionsWidget::class)

    <!-- Statistiques globales (Activité des 7 derniers jours avec nombre total) -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
        @livewire(\App\Filament\Widgets\ActivityChartWidget::class)
    </div>

    <!-- Alertes Récentes et Notifications récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @livewire(\App\Filament\Widgets\RecentActivityWidget::class)
        @livewire(\App\Filament\Widgets\LastAlert::class)
    </div>
</x-filament::page>
