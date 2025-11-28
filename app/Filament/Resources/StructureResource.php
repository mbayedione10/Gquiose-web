<?php

namespace App\Filament\Resources;

use App\Models\Structure;
use App\Models\TypeStructure;
use App\Models\Ville;
use App\Exports\StructuresExport;
use Filament\{Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\StructureResource\Pages;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StructureResource extends Resource
{
    protected static ?string $model = Structure::class;


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = "Structures sanitaires";
    protected static ?string $navigationGroup = "Santé";
    protected static ?string $navigationIcon = 'heroicon-o-office-building';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 0])->schema([


                    Select::make('type_structure_id')
                        ->label("Type de structure")
                        ->rules(['exists:type_structures,id'])
                        ->label("Choisir un type de structure")
                        ->required()
                        ->relationship('typeStructure', 'name')
                        ->searchable()
                        ->placeholder('Type Structure')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->rules(['max:255', 'string'])
                                ->required()
                                ->unique(
                                    'type_structures',
                                    'name',
                                    fn(?TypeStructure $record) => $record
                                )
                                ->placeholder('Name')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Forms\Components\FileUpload::make('icon')
                                ->maxSize(512)
                                ->image()
                                ->required()
                                ->placeholder('Icon')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Toggle::make('status')
                                ->rules(['boolean'])
                                ->required()
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('name')
                        ->rules(['max:255', 'string'])
                        ->label("Nom de la structure")
                        ->required()
                        ->placeholder('Nom de la structure')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->label("Description de la structure")
                        ->placeholder('Description de la structure')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('phone')
                        ->rules(['max:255', 'string'])
                        ->label("Téléphone")
                        ->required()
                        ->unique(
                            'structures',
                            'phone',
                            fn(?Model $record) => $record
                        )
                        ->placeholder('Numéro de téléphone')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Select::make('ville_id')
                        ->rules(['exists:villes,id'])
                        ->required()
                        ->relationship('ville', 'name')
                        ->searchable()
                        ->placeholder('Ville')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->rules(['max:255', 'string'])
                                ->required()
                                ->unique(
                                    'villes',
                                    'name',
                                    fn(?Ville $record) => $record
                                )
                                ->placeholder('Name')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Toggle::make('status')
                                ->rules(['boolean'])
                                ->required()
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('adresse')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Adresse')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Forms\Components\Section::make('Localisation GPS')
                        ->description('Cliquez sur la carte, recherchez une adresse, ou saisissez les coordonnées manuellement')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    TextInput::make('latitude')
                                        ->label("Latitude")
                                        ->required()
                                        ->numeric()
                                        ->step(0.000001)
                                        ->placeholder('Ex: 9.5450')
                                        ->helperText('Entre -90 et 90'),

                                    TextInput::make('longitude')
                                        ->label("Longitude")
                                        ->required()
                                        ->numeric()
                                        ->step(0.000001)
                                        ->placeholder('Ex: -13.6520')
                                        ->helperText('Entre -180 et 180'),
                                ]),

                            Forms\Components\Placeholder::make('map_placeholder')
                                ->label('')
                                ->content(function ($record) {
                                    $lat = $record?->latitude ?? 9.5092;
                                    $lng = $record?->longitude ?? -13.7122;
                                    
                                    return new \Illuminate\Support\HtmlString("
                                        <div id='map-container' style='width: 100%; height: 400px; border-radius: 8px; overflow: hidden; margin-top: 16px;'>
                                            <div id='map' style='width: 100%; height: 100%;'></div>
                                        </div>
                                        
                                        <link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />
                                        <script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>
                                        
                                        <script>
                                            setTimeout(function() {
                                                if (typeof L === 'undefined') return;
                                                
                                                const mapElement = document.getElementById('map');
                                                if (!mapElement || mapElement._leaflet_id) return;
                                                
                                                const map = L.map('map').setView([{$lat}, {$lng}], 13);
                                                let marker = null;
                                                
                                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                    attribution: '© OpenStreetMap'
                                                }).addTo(map);
                                                
                                                function updateMarker(lat, lng) {
                                                    if (marker) map.removeLayer(marker);
                                                    marker = L.marker([lat, lng]).addTo(map);
                                                }
                                                
                                                if ({$lat} && {$lng}) {
                                                    updateMarker({$lat}, {$lng});
                                                }
                                                
                                                map.on('click', function(e) {
                                                    const lat = e.latlng.lat.toFixed(6);
                                                    const lng = e.latlng.lng.toFixed(6);
                                                    
                                                    // Trouver les inputs par leur attribut name
                                                    const latInput = document.querySelector('input[id*=\"latitude\"]');
                                                    const lngInput = document.querySelector('input[id*=\"longitude\"]');
                                                    
                                                    if (latInput && lngInput) {
                                                        latInput.value = lat;
                                                        lngInput.value = lng;
                                                        
                                                        // Déclencher les événements pour Livewire
                                                        latInput.dispatchEvent(new Event('input', { bubbles: true }));
                                                        lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                                                        
                                                        updateMarker(lat, lng);
                                                    }
                                                });
                                                
                                                // Mise à jour de la carte quand les inputs changent
                                                setTimeout(function() {
                                                    const latInput = document.querySelector('input[id*=\"latitude\"]');
                                                    const lngInput = document.querySelector('input[id*=\"longitude\"]');
                                                    
                                                    if (latInput && lngInput) {
                                                        [latInput, lngInput].forEach(input => {
                                                            input.addEventListener('input', function() {
                                                                const lat = parseFloat(latInput.value);
                                                                const lng = parseFloat(lngInput.value);
                                                                if (lat && lng) {
                                                                    map.setView([lat, lng], 13);
                                                                    updateMarker(lat, lng);
                                                                }
                                                            });
                                                        });
                                                    }
                                                }, 500);
                                                
                                                // Fix pour affichage correct de la carte
                                                setTimeout(function() {
                                                    map.invalidateSize();
                                                }, 100);
                                            }, 500);
                                        </script>
                                    ");
                                }),
                        ])
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),


                    Toggle::make('status')
                        ->rules(['boolean'])
                        ->required()
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),


                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Structure")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('description')
                    ->label("Offre")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('phone')
                    ->label("Téléphone")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('typeStructure.name')
                    ->limit(50),

                Tables\Columns\TextColumn::make('ville.name')
                    ->label("Ville")
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('adresse')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\ToggleColumn::make('status'),


            ])
            ->filters([
                DateRangeFilter::make('created_at'),

                SelectFilter::make('type_structure_id')
                    ->relationship('typeStructure', 'name')
                    ->indicator('TypeStructure')
                    ->multiple()
                    ->label('TypeStructure'),

                SelectFilter::make('ville_id')
                    ->relationship('ville', 'name')
                    ->indicator('Ville')
                    ->multiple()
                    ->label('Ville'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Exporter Excel')
                    ->icon('heroicon-o-document-download')
                    ->color('success')
                    ->action(fn () => Excel::download(new StructuresExport(), 'structures_' . date('Y-m-d') . '.xlsx')),

                Tables\Actions\Action::make('export_pdf')
                    ->label('Exporter PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $structures = Structure::with(['typeStructure', 'ville'])->get();
                        $pdf = Pdf::loadView('pdf.structures', compact('structures'));
                        return response()->streamDownload(fn () => print($pdf->output()), 'structures_' . date('Y-m-d') . '.pdf');
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStructures::route('/'),
            'create' => Pages\CreateStructure::route('/create'),
            'view' => Pages\ViewStructure::route('/{record}'),
            'edit' => Pages\EditStructure::route('/{record}/edit'),
        ];
    }
}
