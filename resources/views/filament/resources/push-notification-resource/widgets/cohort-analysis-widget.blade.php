<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 Analyse de cohorte - Engagement par mois d'inscription
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                            Cohorte (Mois d'inscription)
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Utilisateurs
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Notifications reçues
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Moy. par utilisateur
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Taux d'ouverture
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Taux de clic
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">
                            Score d'engagement
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($this->getCohortData() as $cohort)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $cohort['cohort'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                {{ $cohort['users_count'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                {{ $cohort['notifications_received'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                {{ number_format($cohort['avg_per_user'], 1) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $cohort['open_rate'] >= 40 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($cohort['open_rate'] >= 20 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ $cohort['open_rate'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $cohort['click_rate'] >= 30 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($cohort['click_rate'] >= 15 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ $cohort['click_rate'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $cohort['engagement_score'] >= 70 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($cohort['engagement_score'] >= 50 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ number_format($cohort['engagement_score'], 1) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Aucune donnée de cohorte disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
