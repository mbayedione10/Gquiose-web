<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlerteResource\Widgets\AlertOverview;
use App\Models\Alerte;
use App\Exports\AlertesExport;
use Illuminate\Console\View\Components\Alert;
use Filament\{Notifications\Notification, Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
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
            Tabs::make('Informations VBG')->tabs([

                // TAB 1: Informations générales
                Tabs\Tab::make('Informations générales')->schema([
                    Section::make('')->schema([
                        Grid::make(2)->schema([
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

                            Select::make('type_alerte_id')
                                ->label('Type de violence')
                                ->relationship('typeAlerte', 'name')
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->columnSpan(2),

                            Select::make('sous_type_violence_numerique_id')
                                ->label('Sous-type de violence numérique')
                                ->relationship('sousTypeViolenceNumerique', 'nom')
                                ->searchable()
                                ->visible(fn (callable $get) => $get('type_alerte_id'))
                                ->columnSpan(2),

                            Textarea::make('description')
                                ->label('Description')
                                ->rows(5)
                                ->required()
                                ->columnSpan(2),

                            Select::make('utilisateur_id')
                                ->label('Signalée par')
                                ->relationship('utilisateur', 'name')
                                ->searchable()
                                ->required(),

                            Select::make('ville_id')
                                ->label('Ville')
                                ->relationship('ville', 'name')
                                ->searchable()
                                ->required(),

                            TextInput::make('latitude')
                                ->label('Latitude')
                                ->numeric(),

                            TextInput::make('longitude')
                                ->label('Longitude')
                                ->numeric(),

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
                ])->icon('heroicon-o-information-circle'),

                // TAB 2: Violences numériques
                Tabs\Tab::make('Violences numériques')->schema([
                    Section::make('Informations sur les violences technologiques')->schema([
                        Grid::make(2)->schema([
                            TagsInput::make('plateformes')
                                ->label('Plateformes concernées')
                                ->placeholder('Facebook, WhatsApp, Instagram...')
                                ->helperText('Où la violence a eu lieu'),

                            TagsInput::make('nature_contenu')
                                ->label('Nature du contenu')
                                ->placeholder('Messages, Images, Vidéos...')
                                ->helperText('Type de contenu problématique'),

                            Textarea::make('urls_problematiques')
                                ->label('URLs problématiques')
                                ->rows(3)
                                ->columnSpan(2)
                                ->helperText('Liens vers les contenus problématiques'),

                            Textarea::make('comptes_impliques')
                                ->label('Comptes/Pseudonymes impliqués')
                                ->rows(3)
                                ->columnSpan(2)
                                ->helperText('Noms des profils des agresseurs'),

                            Select::make('frequence_incidents')
                                ->label('Fréquence des incidents')
                                ->options([
                                    'unique' => 'Incident unique',
                                    'quotidien' => 'Quotidien',
                                    'hebdomadaire' => 'Hebdomadaire',
                                    'mensuel' => 'Mensuel',
                                    'continu' => 'Continu',
                                ])
                                ->columnSpan(2),
                        ]),
                    ]),
                ])->icon('heroicon-o-device-mobile'),

                // TAB 3: Détails de l'incident
                Tabs\Tab::make('Détails incident')->schema([
                    Section::make('Informations sur l\'incident')->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('date_incident')
                                ->label('Date de l\'incident')
                                ->displayFormat('d/m/Y'),

                            TimePicker::make('heure_incident')
                                ->label('Heure de l\'incident')
                                ->displayFormat('H:i'),

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
                                ])
                                ->columnSpan(2),

                            TagsInput::make('impact')
                                ->label('Impact sur la victime')
                                ->placeholder('Stress, Peur, Dépression...')
                                ->columnSpan(2)
                                ->helperText('Impact psychologique et physique'),
                        ]),
                    ]),
                ])->icon('heroicon-o-clock'),

                // TAB 4: Preuves et conseils
                Tabs\Tab::make('Preuves & Conseils')->schema([
                    Section::make('Preuves')->schema([
                        Placeholder::make('preuves_info')
                            ->label('Fichiers de preuves')
                            ->content(function (?Alerte $record): string {
                                if (!$record || !$record->preuves) {
                                    return 'Aucune preuve fournie';
                                }
                                $count = count($record->preuves);
                                return "{$count} fichier(s) de preuve fourni(s)";
                            }),

                        Repeater::make('preuves')
                            ->label('Liste des preuves')
                            ->schema([
                                TextInput::make('path')
                                    ->label('Chemin du fichier')
                                    ->disabled(),
                            ])
                            ->disabled()
                            ->defaultItems(0)
                            ->hidden(fn (?Alerte $record): bool => !$record || !$record->preuves),
                    ]),

                    Section::make('Conseils de sécurité')->schema([
                        Textarea::make('conseils_securite')
                            ->label('Conseils de sécurité')
                            ->rows(8)
                            ->helperText('Conseils personnalisés selon le type de violence'),

                        Toggle::make('conseils_lus')
                            ->label('Conseils lus par la victime')
                            ->inline(false),
                    ]),
                ])->icon('heroicon-o-shield-check'),

                // TAB 5: Consentement & Anonymat
                Tabs\Tab::make('Consentement')->schema([
                    Section::make('Préférences de la victime')->schema([
                        Grid::make(1)->schema([
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
                ])->icon('heroicon-o-shield-exclamation'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                Tables\Columns\BadgeColumn::make('etat')
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
                    ->icon('heroicon-o-x')
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
                    ->icon('heroicon-o-document-download')
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