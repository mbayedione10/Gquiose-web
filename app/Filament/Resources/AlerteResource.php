<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlerteResource\Widgets\AlertOverview;
use App\Models\Alerte;
use App\Exports\AlertesExport;
use Illuminate\Console\View\Components\Alert;
use Filament\{Notifications\Notification, Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\AlerteResource\Pages;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;

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

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Informations VBG')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([

                // TAB 1: Informations générales
                Tabs\Tab::make('Infos')
                    ->label('Informations générales')
                    ->icon('heroicon-o-information-circle')
                    ->badge(fn (?Alerte $record) => $record?->etat)
                    ->badgeColor(fn (?Alerte $record) => match($record?->etat) {
                        'Confirmée' => 'success',
                        'Rejetée' => 'danger',
                        default => 'warning',
                    })
                    ->schema([
                        Section::make('Identification')
                            ->description('Références et identification de l\'alerte')
                            ->icon('heroicon-o-identification')
                            ->collapsible()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    TextInput::make('ref')
                                        ->label('Référence')
                                        ->default(fn() => 'ALRT-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT))
                                        ->required()
                                        ->unique(ignoreRecord: true),

                                    TextInput::make('numero_suivi')
                                        ->label('Numéro de suivi')
                                        ->default(fn() => 'VBG-' . date('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT))
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                ]),
                            ]),

                        Section::make('Type de violence')
                            ->description('Classification de la violence signalée')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->collapsible()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    Select::make('type_alerte_id')
                                        ->label('Type de violence')
                                        ->relationship('typeAlerte', 'name', fn ($query) => $query->where('status', true))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->reactive()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label('Nom du type de violence')
                                                ->required()
                                                ->unique('type_alertes', 'name')
                                                ->maxLength(255),
                                            Toggle::make('status')
                                                ->label('Actif')
                                                ->default(true),
                                        ])
                                        ->columnSpan(['default' => 1, 'sm' => 2]),

                                    Select::make('sous_type_violence_numerique_id')
                                        ->label('Sous-type de violence numérique')
                                        ->relationship('sousTypeViolenceNumerique', 'nom', fn ($query) => $query->where('status', true))
                                        ->searchable()
                                        ->preload()
                                        ->visible(fn (callable $get) => $get('type_alerte_id'))
                                        ->createOptionForm([
                                            TextInput::make('nom')
                                                ->label('Nom du sous-type')
                                                ->required()
                                                ->maxLength(255),
                                            Textarea::make('description')
                                                ->label('Description')
                                                ->rows(3),
                                            Toggle::make('status')
                                                ->label('Actif')
                                                ->default(true),
                                        ])
                                        ->columnSpan(['default' => 1, 'sm' => 2]),

                                    Textarea::make('description')
                                        ->label('Description')
                                        ->rows(4)
                                        ->required()
                                        ->columnSpan(['default' => 1, 'sm' => 2]),

                                    Select::make('etat')
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

                        Section::make('Signalé par')
                            ->description('Informations sur la personne ayant signalé')
                            ->icon('heroicon-o-user')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    Select::make('utilisateur_id')
                                        ->label('Utilisateur')
                                        ->relationship('utilisateur', 'name')
                                        ->searchable()
                                        ->required(),

                                    Select::make('ville_id')
                                        ->label('Ville')
                                        ->relationship('ville', 'name')
                                        ->searchable()
                                        ->required(),
                                ]),
                            ]),

                        Section::make('Localisation')
                            ->description('Coordonnées géographiques (anonymisées)')
                            ->icon('heroicon-o-map-pin')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])->schema([
                                    TextInput::make('latitude')
                                        ->label('Latitude')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('longitude')
                                        ->label('Longitude')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('quartier')
                                        ->label('Quartier')
                                        ->disabled(),

                                    TextInput::make('commune')
                                        ->label('Commune')
                                        ->disabled(),

                                    Select::make('precision_localisation')
                                        ->label('Précision')
                                        ->options([
                                            'exacte' => 'Exacte',
                                            'approximative' => 'Approximative',
                                        ])
                                        ->default('approximative')
                                        ->disabled(),

                                    TextInput::make('rayon_approximation_km')
                                        ->label('Rayon (km)')
                                        ->numeric()
                                        ->disabled(),
                                ]),
                            ]),
                    ]),

                // TAB 2: Violences numériques
                Tabs\Tab::make('Numérique')
                    ->label('Violences numériques')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Section::make('Plateformes et contenus')
                            ->description('Informations sur les violences technologiques')
                            ->icon('heroicon-o-globe-alt')
                            ->collapsible()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    Select::make('plateformes')
                                        ->label('Plateformes concernées')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->options(fn () => \App\Models\Plateforme::where('status', true)->pluck('nom', 'nom'))
                                        ->helperText('Où la violence a eu lieu')
                                        ->createOptionForm([
                                            TextInput::make('nom')
                                                ->label('Nom de la plateforme')
                                                ->required()
                                                ->unique('plateformes', 'nom')
                                                ->maxLength(255),
                                            Textarea::make('description')
                                                ->label('Description')
                                                ->rows(3),
                                            Toggle::make('status')
                                                ->label('Active')
                                                ->default(true),
                                        ])
                                        ->createOptionUsing(function ($data) {
                                            $plateforme = \App\Models\Plateforme::create($data);
                                            return $plateforme->nom;
                                        }),

                                    Select::make('nature_contenu')
                                        ->label('Nature du contenu')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->options(fn () => \App\Models\NatureContenu::where('status', true)->pluck('nom', 'nom'))
                                        ->helperText('Type de contenu problématique')
                                        ->createOptionForm([
                                            TextInput::make('nom')
                                                ->label('Nom du type de contenu')
                                                ->required()
                                                ->unique('nature_contenus', 'nom')
                                                ->maxLength(255),
                                            Textarea::make('description')
                                                ->label('Description')
                                                ->rows(3),
                                            Toggle::make('status')
                                                ->label('Actif')
                                                ->default(true),
                                        ])
                                        ->createOptionUsing(function ($data) {
                                            $nature = \App\Models\NatureContenu::create($data);
                                            return $nature->nom;
                                        }),

                                    Select::make('frequence_incidents')
                                        ->label('Fréquence des incidents')
                                        ->options([
                                            'unique' => 'Incident unique',
                                            'quotidien' => 'Quotidien',
                                            'hebdomadaire' => 'Hebdomadaire',
                                            'mensuel' => 'Mensuel',
                                            'continu' => 'Continu',
                                        ])
                                        ->columnSpan(['default' => 1, 'sm' => 2]),
                                ]),
                            ]),

                        Section::make('Détails techniques')
                            ->description('URLs et comptes impliqués')
                            ->icon('heroicon-o-link')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Textarea::make('urls_problematiques')
                                    ->label('URLs problématiques')
                                    ->rows(3)
                                    ->helperText('Liens vers les contenus problématiques'),

                                Textarea::make('comptes_impliques')
                                    ->label('Comptes/Pseudonymes impliqués')
                                    ->rows(3)
                                    ->helperText('Noms des profils des agresseurs'),
                            ]),
                    ]),

                // TAB 3: Détails de l'incident
                Tabs\Tab::make('Incident')
                    ->label('Détails incident')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Section::make('Date et heure')
                            ->description('Quand l\'incident s\'est produit')
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    DatePicker::make('date_incident')
                                        ->label('Date de l\'incident')
                                        ->displayFormat('d/m/Y'),

                                    TimePicker::make('heure_incident')
                                        ->label('Heure de l\'incident')
                                        ->displayFormat('H:i'),
                                ]),
                            ]),

                        Section::make('Contexte')
                            ->description('Relation et impact')
                            ->icon('heroicon-o-users')
                            ->collapsible()
                            ->schema([
                                Select::make('relation_agresseur')
                                    ->label('Relation avec l\'agresseur')
                                    ->options([
                                        'conjoint' => 'Conjoint(e)',
                                        'ex_partenaire' => 'Ex-partenaire',
                                        'famille' => 'Membre de la famille',
                                        'collegue' => 'Collègue',
                                        'ami' => 'Ami(e)',
                                        'connaissance' => 'Connaissance',
                                        'inconnu' => 'Inconnu',
                                        'autre' => 'Autre',
                                    ]),

                                TagsInput::make('impact')
                                    ->label('Impact sur la victime')
                                    ->placeholder('Stress, Peur, Dépression...')
                                    ->helperText('Impact psychologique et physique'),
                            ]),
                    ]),

                // TAB 4: Preuves et conseils
                Tabs\Tab::make('Preuves')
                    ->label('Preuves & Conseils')
                    ->icon('heroicon-o-shield-check')
                    ->badge(fn (?Alerte $record) => $record?->preuves ? count($record->preuves) : null)
                    ->schema([
                        Section::make('Fichiers de preuves')
                            ->description('Documents et captures fournis')
                            ->icon('heroicon-o-paper-clip')
                            ->collapsible()
                            ->schema([
                                Placeholder::make('preuves_list')
                                    ->label('')
                                    ->content(function (?Alerte $record): \Illuminate\Support\HtmlString {
                                        if (!$record || !$record->preuves || count($record->preuves) === 0) {
                                            return new \Illuminate\Support\HtmlString(
                                                '<p class="text-gray-500">Aucune preuve fournie</p>'
                                            );
                                        }

                                        $evidenceService = app(\App\Services\VBG\EvidenceSecurityService::class);
                                        $html = '<div class="space-y-4">';

                                        foreach ($record->preuves as $index => $preuve) {
                                            $name = $preuve['original_name'] ?? 'Fichier ' . ($index + 1);
                                            $type = $preuve['type'] ?? 'Inconnu';
                                            $size = isset($preuve['size']) ? round($preuve['size'] / 1024, 2) . ' Ko' : 'N/A';
                                            $isImage = str_starts_with($type, 'image/');

                                            $html .= '<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                            $html .= '<div class="flex items-center justify-between mb-3">';
                                            $html .= '<div>';
                                            $html .= '<p class="font-medium text-sm">' . e($name) . '</p>';
                                            $html .= '<p class="text-xs text-gray-500">' . e($type) . ' • ' . $size . '</p>';
                                            $html .= '</div>';
                                            $html .= '</div>';

                                            // Afficher l'image directement si c'est une image
                                            if ($isImage) {
                                                try {
                                                    $content = $evidenceService->retrieveEvidence($preuve['path']);
                                                    if ($content) {
                                                        $base64 = base64_encode($content);
                                                        $html .= '<img src="data:' . $type . ';base64,' . $base64 . '" class="max-w-full rounded-lg shadow-md" style="max-height: 400px;" alt="' . e($name) . '">';
                                                    } else {
                                                        $html .= '<p class="text-red-500 text-sm">Erreur: impossible de déchiffrer le fichier</p>';
                                                    }
                                                } catch (\Exception $e) {
                                                    $html .= '<p class="text-red-500 text-sm">Erreur: ' . e($e->getMessage()) . '</p>';
                                                }
                                            } else {
                                                $url = url('/preuves/alertes/' . $record->id . '/' . $index);
                                                $html .= '<a href="' . $url . '" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">';
                                                $html .= '📥 Télécharger le fichier';
                                                $html .= '</a>';
                                            }

                                            $html .= '</div>';
                                        }
                                        $html .= '</div>';

                                        return new \Illuminate\Support\HtmlString($html);
                                    }),
                            ]),

                        Section::make('Conseils de sécurité')
                            ->description('Recommandations pour la victime')
                            ->icon('heroicon-o-light-bulb')
                            ->collapsible()
                            ->schema([
                                Textarea::make('conseils_securite')
                                    ->label('Conseils de sécurité')
                                    ->rows(6)
                                    ->helperText('Conseils personnalisés selon le type de violence'),

                                Toggle::make('conseils_lus')
                                    ->label('Conseils lus par la victime')
                                    ->inline(false),
                            ]),
                    ]),

                // TAB 5: Consentement & Anonymat
                Tabs\Tab::make('Consentement')
                    ->icon('heroicon-o-shield-exclamation')
                    ->badge(fn (?Alerte $record) => $record?->anonymat_souhaite ? 'Anonyme' : null)
                    ->badgeColor('warning')
                    ->schema([
                        Section::make('Préférences de confidentialité')
                            ->description('Choix de la victime concernant ses données')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Grid::make(['default' => 1, 'sm' => 2])->schema([
                                    Toggle::make('anonymat_souhaite')
                                        ->label('Anonymat souhaité')
                                        ->inline(false)
                                        ->default(false)
                                        ->helperText('La victime souhaite rester anonyme'),

                                    Toggle::make('consentement_transmission')
                                        ->label('Consentement pour transmission')
                                        ->inline(false)
                                        ->default(true)
                                        ->helperText('Autorisation de transmettre au système national VBG'),
                                ]),
                            ]),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Alerte $record): string => static::getUrl('view', ['record' => $record]))
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('numero_suivi')
                    ->label('N° Suivi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numéro de suivi copié')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-hashtag')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('ref')
                    ->label('Référence')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make("utilisateur.name")
                    ->label("Signalée par")
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make("typeAlerte.name")
                    ->label("Type de violence")
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('description')
                    ->label("Description")
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('anonymat_souhaite')
                    ->label('Anonymat')
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn (Alerte $record): string => $record->anonymat_souhaite ? 'Anonymat souhaité' : 'Pas d\'anonymat'),

                Tables\Columns\TextColumn::make('etat')
                    ->label("État")
                    ->colors([
                        'warning' => static fn ($state): bool => $state === 'Non approuvée',
                        'success' => static fn ($state): bool => $state === 'Confirmée',
                        'danger' => static fn ($state): bool => $state === 'Rejetée',
                    ])
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ville.name')
                    ->label("Ville")
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Date de signalement")
                    ->searchable()
                    ->date("d/m/Y H:i")
                    ->sortable()
                    ->toggleable(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir les détails')
                    ->color('info'),

                Tables\Actions\Action::make('confirmation')
                    ->label("Confirmez")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (Alerte $record){
                        return $record->etat == "Non approuvée";
                    })
                    ->modalHeading("Confirmation")
                    ->modalSubheading("Voulez-vous vraiement confirmer cette alerte ?")
                    ->action(function (Alerte $record){

                        $record->etat = "Confirmée";
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->body("L'alerte qui a pour référence **" .$record->ref. "** vient d'être confirmée")
                            ->send();
                    }),

                Tables\Actions\Action::make('rejeter')
                    ->label("Rejetez")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(function (Alerte $record){
                        return $record->etat == "Non approuvée";
                    })
                    ->modalHeading("Confirmation")
                    ->modalSubheading("Voulez-vous vraiment rejeter cette alerte ?")
                    ->action(function (Alerte $record){

                        $record->etat = "Rejetée";
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->success()
                            ->body("L'alerte qui a pour référence **" .$record->ref. "** vient d'être rejetée")
                            ->send();
                    }),

                Tables\Actions\Action::make('description')
                    ->label("Ajouter des détails")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading("Détails")
                    ->modalSubheading("Rajouter toutes les informations concernant cette alerte ")
                    ->form([
                        Forms\Components\Textarea::make('description')
                            ->label("Information")
                            ->placeholder("Saisissez ici les informations")
                            ->default(function (Alerte $record){
                                return $record->description;
                            })
                            ->required()
                    ])
                    ->action(function (Alerte $record, array $data){

                        $record->description = $data['description'];
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->success()
                            ->body("Une nouvelle information vient d'être rajoutée")
                            ->send();
                    }),


            ])
            ->filters([
                SelectFilter::make('type_alerte_id')
                    ->label('Type de violence')
                    ->relationship('typeAlerte', 'name')
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('etat')
                    ->label('État')
                    ->options([
                        'Non approuvée' => 'Non approuvée',
                        'Confirmée' => 'Confirmée',
                        'Rejetée' => 'Rejetée',
                    ])
                    ->multiple(),

                SelectFilter::make('ville_id')
                    ->label('Ville')
                    ->relationship('ville', 'name')
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('anonymat_souhaite')
                    ->label('Anonymat')
                    ->options([
                        '1' => 'Avec anonymat',
                        '0' => 'Sans anonymat',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Signalée du'),
                        DatePicker::make('created_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Exporter Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn () => Excel::download(new AlertesExport(), 'alertes_' . date('Y-m-d') . '.xlsx')),

                Tables\Actions\Action::make('export_pdf')
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