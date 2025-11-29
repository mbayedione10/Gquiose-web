
<x-filament::page>
    <!-- En-tête du Dashboard -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Vue d'ensemble de l'application</p>
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
