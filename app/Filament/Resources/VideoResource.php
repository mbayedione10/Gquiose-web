<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource\RelationManagers;
use App\Models\Conseil;
use App\Models\Theme;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = "Vidéos";
    protected static ?int $navigationSort = 14;
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make("name")
                            ->label("Nom")
                            ->placeholder("Nom de la vidéo")
                            ->rules(['max:255', 'string'])
                            ->required(),
                        Forms\Components\TextInput::make("url")
                            ->label("Lien")
                            ->placeholder("Lien de la vidéo YouTube")
                            ->unique(ignorable: fn(?Video $record): ?Video => $record)
                            ->rules(['max:255', 'string'])
                            ->required()
                    ])
            ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Nom")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label("Lien")
                    ->searchable()
                    ->sortable()
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
