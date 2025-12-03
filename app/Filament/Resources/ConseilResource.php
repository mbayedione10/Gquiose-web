<?php

namespace App\Filament\Resources;

use App\Models\Conseil;
use App\Filament\Resources\ConseilResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class ConseilResource extends Resource
{
    protected static ?string $model = Conseil::class;
    protected static ?string $navigationLabel = 'Conseils';
    protected static ?string $navigationGroup = 'Contenu Educatif';
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('categorie')
                    ->label('Catégorie')
                    ->options([
                        'SSR' => 'Santé Sexuelle et Reproductive',
                        'VBG' => 'Violences Basées sur le Genre',
                        'Autonomisation' => 'Autonomisation',
                        'Général' => 'Général',
                    ])
                    ->required()
                    ->default('Général'),

                Textarea::make('message')
                    ->label('Conseil')
                    ->placeholder('Entrez votre conseil ici...')
                    ->rows(6)
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SSR' => 'success',
                        'VBG' => 'danger',
                        'Autonomisation' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('message')
                    ->label('Conseil')
                    ->limit(80)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 80 ? $state : null;
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Aucun conseil')
            ->emptyStateDescription('Ajoutez des conseils pour aider les utilisatrices.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConseils::route('/'),
            'create' => Pages\CreateConseil::route('/create'),
            'edit'   => Pages\EditConseil::route('/{record}/edit'),
        ];
    }
}