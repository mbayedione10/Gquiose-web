<?php

namespace App\Filament\Resources;

use App\Models\Article;
use App\Models\Rubrique;
use Filament\{Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
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
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->placeholder("Titre de l'article")
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                RichEditor::make('description')
                    ->label("Description")
                    ->required()
                    ->placeholder('Description')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Select::make('rubrique_id')
                    ->label("Rubrique")
                    ->rules(['exists:rubriques,id'])
                    ->required()
                    ->relationship('rubrique', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label("Rubrique")
                            ->rules(['max:255', 'string'])
                            ->required()
                            ->unique(
                                'rubriques',
                                'name',
                                fn(?Rubrique $record) => $record
                            )
                            ->placeholder('Nom de la rubrique')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                        Toggle::make('status')
                            ->rules(['boolean'])
                            ->required()
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),
                    ])
                    ->searchable()
                    ->options(
                        Rubrique::whereStatus(true)
                            ->pluck('name', 'id')
                    )
                    ->placeholder('Rubrique')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),


                FileUpload::make('image')
                    ->rules(['image', 'max:1024'])
                    ->nullable()
                    ->image()
                    ->placeholder('Image')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('video_url')
                    ->label("Vidéo")
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->placeholder("L'URL de la vidéo YouTube")
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Toggle::make('status')
                    ->rules(['boolean'])
                    ->label("Publier")
                    ->required()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([

                Tables\Columns\ImageColumn::make('image')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label("Titre")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('rubrique.name')
                    ->label("Rubrique")
                    ->sortable()
                    ->limit(50),


                Tables\Columns\ToggleColumn::make('vedette')
                    ->label("Vedette"),

               Tables\Columns\ToggleColumn::make('status')
                    ->label("Publié"),

                Tables\Columns\TextColumn::make('user.name')
                    ->label("Publié par")
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Date de création")
                    ->date('d F Y H:i')

            ])
            ->filters([

                SelectFilter::make('rubrique_id')
                    ->label("Rubrique")
                    ->relationship('rubrique', 'name')
                    ->indicator('Rubrique')
                    ->multiple()
                    ->label('Rubrique'),

                SelectFilter::make('user_id')
                    ->label("Auteur")
                    ->relationship('user', 'name')
                    ->indicator('User')
                    ->multiple()
                    ->label('User'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
