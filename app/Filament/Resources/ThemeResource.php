<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Filament\Resources\ThemeResource\Pages;
use App\Filament\Resources\ThemeResource\RelationManagers;
use App\Models\Thematique;
use App\Models\Theme;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = "Thèmes";
    protected static ?string $navigationGroup = "Forum";
    protected static ?int $navigationSort = 75;
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->label("Thématique")
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(ignorable: fn(?Theme $record): ?Theme => $record)
                        ->placeholder('Nom de la thématique')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),
                    Toggle::make('status')
                        ->label("Activée")
                        ->rules(['boolean'])
                        ->required()
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),
                ]),
            ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Nom")
                    ->searchable()
                    ->limit(50),
                /*Tables\Columns\TextColumn::make('questions_count')
                    ->label("Questions")
                    ->sortable()
                    ->counts('questions'),*/
                Tables\Columns\IconColumn::make('status')
                    ->label("Statut")
                    ->boolean(),
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
            'index' => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit' => Pages\EditTheme::route('/{record}/edit'),
        ];
    }
}
