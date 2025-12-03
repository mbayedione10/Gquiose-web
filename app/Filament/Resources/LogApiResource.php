<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Filament\Resources\LogApiResource\Pages;
use App\Filament\Resources\LogApiResource\RelationManagers;
use App\Models\LogApi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class LogApiResource extends Resource
{
    protected static ?string $model = LogApi::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = "Monitoring";
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        return false;
    }
    public static function canDelete(Model $record): bool
    {
        return false;
    }
    public static function canDeleteAny(): bool
    {
        return false;
    }
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('method')
                    ->label("Méthode")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uri')
                    ->label("API")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label("Date")
                    ->date('d F Y H:i')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListLogApis::route('/'),
            'create' => Pages\CreateLogApi::route('/create'),
            'edit' => Pages\EditLogApi::route('/{record}/edit'),
        ];
    }
}
