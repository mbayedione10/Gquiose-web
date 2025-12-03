<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Models\NatureContenu;
use Filament\{Tables, Forms};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\NatureContenuResource\Pages;
class NatureContenuResource extends Resource
{
    protected static ?string $model = NatureContenu::class;
    protected static ?string $navigationLabel = "Nature du contenu";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 14;
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('nom')
                        ->label('Nom du type de contenu')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(65535),
                    Toggle::make('status')
                        ->label('Actif')
                        ->default(true)
                        ->inline(false),
                ]),
            ]),
        ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Statut')
                    ->boolean()
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
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
            'index' => Pages\ListNatureContenus::route('/'),
            'create' => Pages\CreateNatureContenu::route('/create'),
            'edit' => Pages\EditNatureContenu::route('/{record}/edit'),
        ];
    }
}
