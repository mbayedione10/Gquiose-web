<?php

namespace App\Filament\Resources;

use App\Models\Information;
use App\Filament\Resources\InformationResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;

class InformationResource extends Resource
{
    protected static ?string $model = Information::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = 'Informations générales';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TagsInput::make('email_alerte')
                    ->label('Courriels de notification d\'alerte')
                    ->placeholder('Saisir un email et appuyer sur Entrée')
                    ->helperText('Ces adresses recevront un email à chaque alerte signalée')
                    ->separator(',')
                    ->columnSpanFull(),

                TextInput::make('rendez_vous')
                    ->label('URL de prise de rendez-vous')
                    ->url()
                    ->required()
                    ->placeholder('https://...'),

                TextInput::make('structure_url')
                    ->label('URL des structures sanitaires')
                    ->url()
                    ->placeholder('https://...')
                    ->nullable(),

                TextInput::make('numero_cybercriminalite')
                    ->label('Numéro Cybercriminalité')
                    ->tel()
                    ->placeholder('Ex: 117 ou +224 6XX XXX XXX')
                    ->helperText('Numéro officiel pour signaler la cybercriminalité en Guinée')
                    ->nullable(),

                TextInput::make('email_cybercriminalite')
                    ->label('Email Cybercriminalité')
                    ->email()
                    ->placeholder('cybercrime@police.gov.gn')
                    ->helperText('Adresse officielle pour les signalements')
                    ->nullable(),

                FileUpload::make('image')
                    ->label('Bannière principale')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', '4:3'])
                    ->maxSize(2048)
                    ->directory('informations')
                    ->visibility('public')
                    ->required(),

                FileUpload::make('splash')
                    ->label('Image de démarrage (Splash Screen)')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('informations')
                    ->visibility('public')
                    ->nullable(),

                Toggle::make('status')
                    ->label('Activé')
                    ->default(true)
                    ->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Bannière')
                    ->height(60)
                    ->width(100),

                TagsColumn::make('email_alerte')
                    ->label('Notifications alerte')
                    ->separator(',')
                    ->limit(3),

                TextColumn::make('rendez_vous')
                    ->label('Rendez-vous')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->icon('heroicon-o-link'),

                TextColumn::make('structure_url')
                    ->label('Structures sanitaires')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->icon('heroicon-o-link'),

                TextColumn::make('numero_cybercriminalite')
                    ->label('N° Cybercriminalité')
                    ->placeholder('—')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('email_cybercriminalite')
                    ->label('Email Cybercriminalité')
                    ->placeholder('—')
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInformation::route('/'),
            'create' => Pages\CreateInformation::route('/create'),
            'edit'   => Pages\EditInformation::route('/{record}/edit'),
        ];
    }
}