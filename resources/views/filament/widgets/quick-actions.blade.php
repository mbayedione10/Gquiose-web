
<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Actions Rapides</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Créer un utilisateur -->
                <a href="{{ route('filament.resources.utilisateurs.create') }}" 
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-white">Nouvel Utilisateur</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ajouter un compte</div>
                    </div>
                </a>

                <!-- Nouvelle alerte -->
                <a href="{{ route('filament.resources.alertes.index') }}" 
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-white">Gérer Alertes</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Voir les signalements</div>
                    </div>
                </a>

                <!-- Nouvel article -->
                <a href="{{ route('filament.resources.articles.create') }}" 
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-white">Nouvel Article</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Publier du contenu</div>
                    </div>
                </a>

                <!-- Statistiques -->
                <a href="{{ route('filament.resources.evaluation-stats.index') }}" 
                   class="group flex items-center gap-3 p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 dark:text-white">Statistiques</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Analyses détaillées</div>
                    </div>
                </a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
