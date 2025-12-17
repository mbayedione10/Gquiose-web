<?php

namespace App\Filament\Resources;

use App\Models\Ville;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\VilleResource\Pages;

class VilleResource extends Resource
{
    protected static ?string $model = Ville::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 70;
    protected static ?string $navigationGroup = "Configuration";

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 0])->schema([
                    TextInput::make('name')
                        ->label('Nom de la ville')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(
                            'villes',
                            'name',
                            fn(?Ville $record) => $record
                        )
                        ->placeholder('Ex: Conakry, Labé, Kankan, etc.')
                        ->helperText('Entrez le nom de la ville')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Toggle::make('status')
                        ->label('Statut actif')
                        ->rules(['boolean'])
                        ->required()
                        ->default(true)
                        ->helperText('Activer ou désactiver cette ville')
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
                    ->label('Nom de la ville')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50)
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Statut')
                    ->toggleable()
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('structures_count')
                    ->label('Structures')
                    ->counts('structures')
                    ->color('success')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('alertes_count')
                    ->label('Alertes')
                    ->counts('alertes')
                    ->color('warning')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Statut')
                    ->placeholder('Toutes')
                    ->trueLabel('Actives')
                    ->falseLabel('Inactives'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            VilleResource\RelationManagers\StructuresRelationManager::class,
            VilleResource\RelationManagers\AlertesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVilles::route('/'),
            'create' => Pages\CreateVille::route('/create'),
            'view' => Pages\ViewVille::route('/{record}'),
            'edit' => Pages\EditVille::route('/{record}/edit'),
        ];
    }
}
