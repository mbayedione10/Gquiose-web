
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
    <div class="mb-6 rounded-2xl shadow-2xl p-8" style="background: linear-gradient(to right, #2563eb, #4f46e5, #7c3aed);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2" style="color: #ffffff;">
                    👋 Bienvenue, {{ auth()->user()->name }}
                </h1>
                <p class="text-lg" style="color: rgba(255, 255, 255, 0.8);">
                    Tableau de bord administrateur - {{ now()->format('d M Y') }}
                </p>
            </div>
            <div class="text-right" style="color: #ffffff;">
                <div class="text-5xl font-bold">{{ now()->format('H:i') }}</div>
                <div style="color: rgba(255, 255, 255, 0.7);">{{ now()->translatedFormat('l') }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Métriques clés - Grid moderne -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Utilisateurs -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #3b82f6;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #3b82f6, #2563eb);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #111827;">{{ number_format($totalUtilisateurs) }}</div>
                    </div>
                </div>
                <div class="text-sm" style="color: #4b5563;">Utilisateurs inscrits</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #dbeafe; color: #1e40af;">
                    {{ $utilisateursActifs }} actifs
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #f59e0b;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #f59e0b, #ea580c);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #111827;">{{ number_format($totalAlertes) }}</div>
                    </div>
                </div>
                <div class="text-sm" style="color: #4b5563;">Alertes signalées</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #fef3c7; color: #92400e;">
                    {{ $alertesConfirmees }} confirmées
                </div>
            </div>
        </div>

        <!-- Contenus -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #10b981;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #10b981, #0d9488);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #111827;">{{ number_format($totalArticles + $totalVideos) }}</div>
                    </div>
                </div>
                <div class="text-sm" style="color: #4b5563;">Contenus publiés</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #d1fae5; color: #065f46;">
                    {{ $totalArticles }} articles • {{ $totalVideos }} vidéos
                </div>
            </div>
        </div>

        <!-- Structures & Services -->
        <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all duration-300" style="background-color: #ffffff; border-left: 4px solid #a855f7;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="rounded-lg flex items-center justify-center shadow-lg" style="width: 3.5rem; height: 3.5rem; background: linear-gradient(to bottom right, #a855f7, #9333ea);">
                        <svg style="width: 2rem; height: 2rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: #111827;">{{ number_format($totalStructures) }}</div>
                    </div>
                </div>
                <div class="text-sm" style="color: #4b5563;">Structures d'aide</div>
                <div class="mt-2 text-xs px-3 py-1 rounded-full inline-block" style="background-color: #f3e8ff; color: #6b21a8;">
                    Centres de santé
                </div>
            </div>
        </div>
    </div>



    <!-- 4. Actions Rapides -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\QuickActionsWidget::class)
    </div>

    <!-- 5. Répartition par tranche d'âge -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\AgeRangeStatsWidget::class)
    </div>

    <!-- 6. Activité des 7 derniers jours (statistiques) -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-6">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
            @livewire(\App\Filament\Widgets\ActivityChartWidget::class)
        </div>
    </div>

    <!-- 6. Alertes Récentes -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\LastAlert::class)
    </div>

    <!-- 7. 15 derniers articles -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\RecentArticlesWidget::class)
    </div>

    <!-- 8. Notifications récentes -->
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\RecentNotificationsWidget::class)
    </div>
</x-filament::page>
