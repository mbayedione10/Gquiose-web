
<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Actions Rapides</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Notifications Push -->
                <a href="{{ route('filament.resources.push-notifications.create') }}"
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-cyan-50 to-blue-100 dark:from-cyan-900/20 dark:to-blue-800/20 rounded-xl hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-cyan-200 dark:border-cyan-700">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-cyan-600 transition-colors">Notifications</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Envoyer notification</div>
                    </div>
                </a>

                <!-- Nouvelle alerte -->
                <a href="{{ route('filament.resources.alertes.index') }}"
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-rose-50 to-red-100 dark:from-rose-900/20 dark:to-red-800/20 rounded-xl hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-rose-200 dark:border-rose-700">
                    <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-red-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-rose-600 transition-colors">Gérer Alertes</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Voir les signalements</div>
                    </div>
                </a>

                <!-- Nouvel article -->
                <a href="{{ route('filament.resources.articles.create') }}"
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-emerald-50 to-teal-100 dark:from-emerald-900/20 dark:to-teal-800/20 rounded-xl hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-emerald-200 dark:border-emerald-700">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 transition-colors">Nouvel Article</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Publier du contenu</div>
                    </div>
                </a>

                <!-- Statistiques -->
                <a href="{{ route('filament.resources.evaluation-stats.index') }}"
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-violet-50 to-purple-100 dark:from-violet-900/20 dark:to-purple-800/20 rounded-xl hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-violet-200 dark:border-violet-700">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-violet-600 transition-colors">Statistiques</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Analyses détaillées</div>
                    </div>
                </a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
