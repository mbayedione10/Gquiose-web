<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;

use App\Models\Article;
use App\Models\Rubrique;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\ArticleResource\Pages;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationLabel = "Articles";
    protected static ?string $navigationGroup = "Blog";
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('title')
                    ->label("Titre")
                    ->required()
                    ->maxLength(255),

                RichEditor::make('description')
                    ->label("Description")
                    ->required()
                    ->columnSpanFull(),

                Select::make('rubrique_id')
                    ->label("Rubrique")
                    ->required()
                    ->relationship('rubrique', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label("Nom de la rubrique")
                            ->required()
                            ->maxLength(255)
                            ->unique('rubriques', 'name'),

                        Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ]),

                FileUpload::make('image')
                    ->image()
                    ->maxSize(1024)
                    ->directory('articles')
                    ->visibility('public'),

                TextInput::make('video_url')
                    ->label("Vidéo (URL YouTube)")
                    ->url()
                    ->maxLength(255),

                Toggle::make('status')
                    ->label("Publier")
                    ->required()
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                ImageColumn::make('image')
                    ->circular(),

                TextColumn::make('title')
                    ->label("Titre")
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('rubrique.name')
                    ->label("Rubrique")
                    ->sortable(),

                ToggleColumn::make('vedette')
                    ->label("Vedette"),

                ToggleColumn::make('status')
                    ->label("Publié"),

                TextColumn::make('user.name')
                    ->label("Publié par"),

                TextColumn::make('created_at')
                    ->label("Créé le")
                    ->dateTime('d F Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('rubrique_id')
                    ->label("Rubrique")
                    ->relationship('rubrique', 'name')
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('user_id')
                    ->label("Auteur")
                    ->relationship('user', 'name')
                    ->multiple()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view'   => Pages\ViewArticle::route('/{record}'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}