
<x-filament::page>
    <div class="space-y-6">
        <!-- En-tête du Dashboard -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 text-white">
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

        <!-- Widgets -->
        <x-filament::widgets :widgets="$this->getHeaderWidgets()" :columns="$this->getHeaderWidgetsColumns()" />
        
        <x-filament::widgets :widgets="$this->getFooterWidgets()" :columns="$this->getFooterWidgetsColumns()" />
    </div>
</x-filament::page>
