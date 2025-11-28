
<div x-data="{
    map: null,
    marker: null,
    lat: @entangle($attributes->wire('model').'.latitude'),
    lng: @entangle($attributes->wire('model').'.longitude'),
    
    initMap() {
        const defaultLat = this.lat || 9.5092;
        const defaultLng = this.lng || -13.7122;
        
        this.map = L.map('map-{{ $getId() }}').setView([defaultLat, defaultLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
        
        if (this.lat && this.lng) {
            this.addMarker(this.lat, this.lng);
        }
        
        this.map.on('click', (e) => {
            this.lat = e.latlng.lat.toFixed(6);
            this.lng = e.latlng.lng.toFixed(6);
            this.addMarker(e.latlng.lat, e.latlng.lng);
            this.$dispatch('location-updated', { lat: this.lat, lng: this.lng });
        });
    },
    
    addMarker(lat, lng) {
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }
        this.marker = L.marker([lat, lng]).addTo(this.map);
    },
    
    searchAddress() {
        const address = document.getElementById('address-search-{{ $getId() }}').value;
        
        if (!address) return;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}, Guinée`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const result = data[0];
                    this.lat = parseFloat(result.lat).toFixed(6);
                    this.lng = parseFloat(result.lon).toFixed(6);
                    this.map.setView([this.lat, this.lng], 15);
                    this.addMarker(this.lat, this.lng);
                    this.$dispatch('location-updated', { lat: this.lat, lng: this.lng });
                } else {
                    alert('Adresse non trouvée');
                }
            })
            .catch(error => {
                console.error('Erreur de géocodage:', error);
                alert('Erreur lors de la recherche');
            });
    }
}" x-init="setTimeout(() => initMap(), 100)">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <div class="space-y-4">
        <!-- Recherche d'adresse -->
        <div class="flex gap-2">
            <input 
                type="text" 
                id="address-search-{{ $getId() }}"
                placeholder="Rechercher une adresse (ex: Matoto, Conakry)"
                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
            />
            <button 
                type="button"
                @click="searchAddress()"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
            >
                🔍 Rechercher
            </button>
        </div>
        
        <!-- Carte interactive -->
        <div 
            id="map-{{ $getId() }}" 
            class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-600"
            style="z-index: 0;"
        ></div>
        
        <!-- Affichage des coordonnées -->
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                <strong>Instructions :</strong> Cliquez sur la carte pour sélectionner un emplacement, 
                recherchez une adresse, ou saisissez les coordonnées manuellement ci-dessous.
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Latitude</label>
                    <input 
                        type="number" 
                        step="0.000001"
                        x-model="lat"
                        @input="if(lng) { map.setView([lat, lng], 13); addMarker(lat, lng); }"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Longitude</label>
                    <input 
                        type="number" 
                        step="0.000001"
                        x-model="lng"
                        @input="if(lat) { map.setView([lat, lng], 13); addMarker(lat, lng); }"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
