
<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold" style="color: #111827;">
                    <svg class="inline-block w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Actions Rapides
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Nouvelle Alerte -->
                <a href="{{ route('filament.admin.resources.alertes.index') }}"
                   class="group flex items-center gap-3 p-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105"
                   style="background: linear-gradient(135deg, #fff1f2 0%, #fecaca 100%); border: 2px solid #fecdd3;">
                    <div class="rounded-lg flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform" style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold group-hover:text-red-700 transition-colors" style="color: #991b1b;">Alertes VBG</div>
                        <div class="text-sm font-medium" style="color: #6b7280;">Gérer les signalements</div>
                    </div>
                </a>

                <!-- Nouvel article -->
                <a href="{{ route('filament.admin.resources.articles.create') }}"
                   class="group flex items-center gap-3 p-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105"
                   style="background: linear-gradient(135deg, #ecfdf5 0%, #a7f3d0 100%); border: 2px solid #6ee7b7;">
                    <div class="rounded-lg flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform" style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold group-hover:text-green-700 transition-colors" style="color: #047857;">Nouvel Article</div>
                        <div class="text-sm font-medium" style="color: #6b7280;">Créer du contenu</div>
                    </div>
                </a>

                <!-- Notifications Push -->
                <a href="{{ route('filament.admin.resources.push-notifications.create') }}"
                   class="group flex items-center gap-3 p-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105"
                   style="background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%); border: 2px solid #60a5fa;">
                    <div class="rounded-lg flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform" style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold group-hover:text-blue-700 transition-colors" style="color: #1d4ed8;">Notification</div>
                        <div class="text-sm font-medium" style="color: #6b7280;">Envoyer aux utilisateurs</div>
                    </div>
                </a>

                <!-- Statistiques -->
                <a href="{{ \App\Filament\Resources\EvaluationStatsResource::getUrl() }}"
                   class="group flex items-center gap-3 p-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105"
                   style="background: linear-gradient(135deg, #f3e8ff 0%, #d8b4fe 100%); border: 2px solid #c084fc;">
                    <div class="rounded-lg flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform" style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold group-hover:text-purple-700 transition-colors" style="color: #7e22ce;">Statistiques</div>
                        <div class="text-sm font-medium" style="color: #6b7280;">Analyses détaillées</div>
                    </div>
                </a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
