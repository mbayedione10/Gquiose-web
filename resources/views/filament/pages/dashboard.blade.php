
<x-filament::page>
    @php
        // Statistiques globales
        $totalUtilisateurs = \App\Models\Utilisateur::count();
        $utilisateursActifs = \App\Models\Utilisateur::where('status', true)->count();
        $totalAlertes = \App\Models\Alerte::count();
        $alertesConfirmees = \App\Models\Alerte::where('etat', 'Confirmée')->count();
        $totalArticles = \App\Models\Article::count();
        $totalVideos = \App\Models\Video::count();
        $totalStructures = \App\Models\Structure::count();
    @endphp

    <!-- 1. En-tête du Dashboard -->
    <div class="mb-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-2xl shadow-2xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2 text-white">
                    👋 Bienvenue, {{ auth()->user()->name }}
                </h1>
                <p class="text-white/80 text-lg">
                    Tableau de bord administrateur - {{ now()->format('d M Y') }}
                </p>
            </div>
            <div class="text-right text-white">
                <div class="text-5xl font-bold">{{ now()->format('H:i') }}</div>
                <div class="text-white/70">{{ now()->translatedFormat('l') }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Métriques clés - Grid moderne -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Utilisateurs -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalUtilisateurs) }}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Utilisateurs inscrits</div>
                <div class="mt-2 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full inline-block">
                    {{ $utilisateursActifs }} actifs
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-amber-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalAlertes) }}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Alertes signalées</div>
                <div class="mt-2 text-xs bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 px-3 py-1 rounded-full inline-block">
                    {{ $alertesConfirmees }} confirmées
                </div>
            </div>
        </div>

        <!-- Contenus -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-emerald-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalArticles + $totalVideos) }}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Contenus publiés</div>
                <div class="mt-2 text-xs bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 px-3 py-1 rounded-full inline-block">
                    {{ $totalArticles }} articles • {{ $totalVideos }} vidéos
                </div>
            </div>
        </div>

        <!-- Structures & Services -->
        <div class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-purple-500">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalStructures) }}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Structures d'aide</div>
                <div class="mt-2 text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full inline-block">
                    Centres de santé
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Actions Rapides -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\QuickActionsWidget::class)
    </div>

    <!-- 4. Activité des 7 derniers jours (statistiques) -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-6">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
            @livewire(\App\Filament\Widgets\ActivityChartWidget::class)
        </div>
    </div>

    <!-- 5. Alertes Récentes -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\LastAlert::class)
    </div>

    <!-- 6. 15 derniers articles -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\RecentArticlesWidget::class)
    </div>

    <!-- 7. Notifications récentes -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\RecentNotificationsWidget::class)
    </div>
</x-filament::page>
