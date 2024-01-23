<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformationResource\Pages;
use App\Filament\Resources\InformationResource\RelationManagers;
use App\Models\Information;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InformationResource extends Resource
{
    protected static ?string $model = Information::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([

                        Forms\Components\TextInput::make('email_alerte')
                            ->label("Courriel de notification")
                            ->placeholder("Saisir le courriel de notification")
                            ->helperText("Ce courriel recevra un email lorsqu'une alerte est signalée")
                            ->nullable()
                            ->email(),

                        Forms\Components\TextInput::make("rendez_vous")
                            ->required()
                            ->placeholder("L'URL de prise de rendez-vous")
                            ->label("Rendez-vous"),

                        Forms\Components\TextInput::make("structure_url")
                            ->nullable()
                            ->placeholder("L'URL de prise des structures sanitaires")
                            ->label("Structures sanitaires"),

                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->image()
                            ->maxSize(512),

                        Forms\Components\Toggle::make('status')
                            ->label("Activé")
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label("Image"),

                Tables\Columns\TextColumn::make('email_alerte')
                    ->searchable()
                    ->label("Image de notification"),

                Tables\Columns\TextColumn::make('rendez_vous')
                    ->label("Rendez-vous"),

                Tables\Columns\TextColumn::make('structure_url')
                    ->label("Structure sanitaire"),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInformation::route('/'),
            'create' => Pages\CreateInformation::route('/create'),
            'edit' => Pages\EditInformation::route('/{record}/edit'),
        ];
    }
}
