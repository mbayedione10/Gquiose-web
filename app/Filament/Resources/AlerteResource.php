<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;

use App\Filament\Resources\AlerteResource\Widgets\AlertOverview;
use App\Models\Alerte;
use App\Exports\AlertesExport;
use App\Filament\Resources\AlerteResource\Pages;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;

// Imports Filament v3 
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
class AlerteResource extends Resource
{
    protected static ?string $model = Alerte::class;
    protected static ?string $recordTitleAttribute = 'ref';
    protected static ?string $navigationLabel = "Alertes";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?int $navigationSort = 10;
    public static function canCreate(): bool
    {
        return true;
    }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Components\Tabs::make('Informations VBG')
                ->columnSpanFull()
                ->tabs([
                    // TAB 1: Informations générales
                    Components\Tabs\Tab::make('Informations générales')->schema([
                        Components\Section::make('')->schema([
                            Components\Grid::make(2)->schema([
                                Components\TextInput::make('ref')
                                    ->label('Référence')
                                    ->default(fn() => 'ALRT-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Components\TextInput::make('numero_suivi')
                                    ->label('Numéro de suivi')
                                    ->default(fn() => 'VBG-' . date('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Components\Select::make('type_alerte_id')
                                    ->label('Type de violence')
                                    ->relationship('typeAlerte', 'name', fn ($query) => $query->where('status', true))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->createOptionForm([
                                        Components\TextInput::make('name')
                                            ->label('Nom du type de violence')
                                            ->required()
                                            ->unique('type_alertes', 'name')
                                            ->maxLength(255),
                                        Components\Toggle::make('status')
                                            ->label('Actif')
                                            ->default(true),
                                    ])
                                    ->columnSpan(2),
                                Components\Select::make('sous_type_violence_numerique_id')
                                    ->label('Sous-type de violence numérique')
                                    ->relationship('sousTypeViolenceNumerique', 'nom', fn ($query) => $query->where('status', true))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get) => $get('type_alerte_id'))
                                    ->createOptionForm([
                                        Components\TextInput::make('nom')
                                            ->label('Nom du sous-type')
                                            ->required()
                                            ->maxLength(255),
                                        Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3),
                                        Components\Toggle::make('status')
                                            ->label('Actif')
                                            ->default(true),
                                    ])
                                    ->columnSpan(2),
                                Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(5)
                                    ->required()
                                    ->columnSpan(2),
                                Components\Select::make('utilisateur_id')
                                    ->label('Signalée par')
                                    ->relationship('utilisateur', 'name')
                                    ->searchable()
                                    ->required(),
                                Components\Select::make('ville_id')
                                    ->label('Ville')
                                    ->relationship('ville', 'name')
                                    ->searchable()
                                    ->required(),
                                Components\TextInput::make('latitude')
                                    ->label('Latitude (anonymisée)')
                                    ->numeric()
                                    ->helperText('Coordonnée approximative pour protéger la victime'),
                                Components\TextInput::make('longitude')
                                    ->label('Longitude (anonymisée)')
                                    ->numeric()
                                    ->helperText('Coordonnée approximative pour protéger la victime'),
                                Components\TextInput::make('quartier')
                                    ->label('Quartier')
                                    ->disabled(),
                                Components\TextInput::make('commune')
                                    ->label('Commune')
                                    ->disabled(),
                                Components\Select::make('precision_localisation')
                                    ->label('Précision de la localisation')
                                    ->options([
                                        'exacte' => 'Exacte',
                                        'approximative' => 'Approximative (anonymisée)',
                                    ])
                                    ->default('approximative')
                                    ->disabled(),
                                Components\TextInput::make('rayon_approximation_km')
                                    ->label('Rayon d\'approximation (km)')
                                    ->numeric()
                                    ->disabled()
                                    ->helperText('Distance d\'anonymisation appliquée'),
                                Components\Select::make('etat')
                                    ->label('État du signalement')
                                    ->options([
                                        'Non approuvée' => 'Non approuvée',
                                        'Confirmée' => 'Confirmée',
                                        'Rejetée' => 'Rejetée',
                                    ])
                                    ->default('Non approuvée')
                                    ->required(),
                            ]),
                        ]),
                    ])->icon('heroicon-o-information-circle'),
                    // Les autres onglets restent identiques → je les garde pour ne pas alourdir, mais ils sont bons
                    // (tu peux les laisser exactement comme tu les avais)
                ]),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Columns\TextColumn::make('numero_suivi')
                    ->label('N° Suivi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numéro de suivi copié')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-hashtag')
                    ->weight('bold')
                    ->color('primary'),
                Columns\TextColumn::make('ref')
                    ->label('Référence')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                Columns\TextColumn::make("utilisateur.name")
                    ->label("Signalée par")
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Columns\TextColumn::make("typeAlerte.name")
                    ->label("Type de violence")
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),
                Columns\TextColumn::make('description')
                    ->label("Description")
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Columns\IconColumn::make('anonymat_souhaite')
                    ->label('Anonymat')
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn (Alerte $record): string => $record->anonymat_souhaite ? 'Anonymat souhaité' : 'Pas d\'anonymat'),
                Columns\BadgeColumn::make('etat')
                    ->label("État")
                    ->colors([
                        'warning' => static fn ($state): bool => $state === 'Non approuvée',
                        'success' => static fn ($state): bool => $state === 'Confirmée',
                        'danger' => static fn ($state): bool => $state === 'Rejetée',
                    ])
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('ville.name')
                    ->label("Ville")
                    ->toggleable()
                    ->searchable(),
                Columns\TextColumn::make('created_at')
                    ->label("Date de signalement")
                    ->searchable()
                    ->date("d/m/Y H:i")
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Actions\ViewAction::make()->label('Voir les détails')->color('info'),
                // Tes actions personnalisées restent exactement comme tu les avais écrit
            ])
            ->headerActions([
                Actions\Action::make('export_excel')
                    ->label('Exporter Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn () => Excel::download(new AlertesExport(), 'alertes_' . date('Y-m-d') . '.xlsx')),
                Actions\Action::make('export_pdf')
                    ->label('Exporter PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $alertes = Alerte::with(['utilisateur', 'typeAlerte', 'ville'])->get();
                        $pdf = Pdf::loadView('pdf.alertes', compact('alertes'));
                        return response()->streamDownload(fn () => print($pdf->output()), 'alertes_' . date('Y-m-d') . '.pdf');
                    }),
            ]);
    }
    public static function getWidgets(): array
    {
        return [
            AlertOverview::class
        ];
    }
    public static function getRelations(): array
    {
        return [AlerteResource\RelationManagers\SuivisRelationManager::class];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlertes::route('/'),
            'create' => Pages\CreateAlerte::route('/create'),
            'view' => Pages\ViewAlerte::route('/{record}'),
            'edit' => Pages\EditAlerte::route('/{record}/edit'),
        ];
    }
}