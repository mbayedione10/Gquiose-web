<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = "Vidéos";

    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make("name")
                            ->label("Titre")
                            ->placeholder("Titre de la vidéo")
                            ->rules(['max:255', 'string'])
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Textarea::make("description")
                            ->label("Description")
                            ->placeholder("Description de la vidéo")
                            ->rows(3)
                            ->columnSpan(2),

                        Forms\Components\Select::make("type")
                            ->label("Type de vidéo")
                            ->options([
                                'youtube' => 'Lien YouTube',
                                'file' => 'Fichier uploadé',
                            ])
                            ->default('youtube')
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make("status")
                            ->label("Active")
                            ->default(true)
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Vidéo YouTube')
                    ->schema([
                        Forms\Components\TextInput::make("url")
                            ->label("Lien YouTube")
                            ->placeholder("https://www.youtube.com/watch?v=...")
                            ->url()
                            ->rules(['max:255', 'string'])
                            ->required(fn ($get) => $get('type') === 'youtube'),
                    ])
                    ->visible(fn ($get) => $get('type') === 'youtube'),

                Forms\Components\Section::make('Fichier vidéo')
                    ->description('Formats acceptés: MP4 (H.264), résolution min 720p, max 500 Mo')
                    ->schema([
                        Forms\Components\FileUpload::make("video_file")
                            ->label("Fichier vidéo")
                            ->disk('public')
                            ->directory('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo'])
                            ->maxSize(512000)
                            ->required(fn ($get) => $get('type') === 'file'),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make("duration")
                                ->label("Durée")
                                ->placeholder("Ex: 10:30"),

                            Forms\Components\Select::make("resolution")
                                ->label("Résolution")
                                ->options([
                                    '720p' => '720p (HD)',
                                    '1080p' => '1080p (Full HD)',
                                    '1440p' => '1440p (2K)',
                                    '2160p' => '2160p (4K)',
                                ]),

                            Forms\Components\TextInput::make("file_size")
                                ->label("Taille (octets)")
                                ->numeric()
                                ->disabled(),
                        ]),
                    ])
                    ->visible(fn ($get) => $get('type') === 'file'),

                Forms\Components\Section::make('Miniature')
                    ->schema([
                        Forms\Components\FileUpload::make("thumbnail")
                            ->label("Image miniature")
                            ->disk('public')
                            ->directory('videos/thumbnails')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->helperText("Ratio 16:9 recommandé. Générée automatiquement pour YouTube si non fournie."),
                    ]),

                Forms\Components\Section::make('Accessibilité')
                    ->description('Sous-titres et audiodescription pour l\'accessibilité')
                    ->schema([
                        Forms\Components\FileUpload::make("subtitle_file")
                            ->label("Fichier sous-titres (SRT)")
                            ->disk('public')
                            ->directory('videos/subtitles')
                            ->acceptedFileTypes(['.srt', 'text/plain', 'application/x-subrip'])
                            ->helperText("Format SRT, encodage UTF-8, max 42 caractères par ligne"),

                        Forms\Components\FileUpload::make("audiodescription_file")
                            ->label("Audiodescription (MP3/WAV)")
                            ->disk('public')
                            ->directory('videos/audiodescriptions')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/x-wav'])
                            ->helperText("Format MP3 ou WAV, bitrate min 128 kbps, synchronisé avec la vidéo"),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label("Miniature")
                    ->circular(false)
                    ->width(80)
                    ->height(45),

                Tables\Columns\TextColumn::make('name')
                    ->label("Titre")
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('type')
                    ->label("Type")
                    ->colors([
                        'danger' => 'youtube',
                        'success' => 'file',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'youtube' ? 'YouTube' : 'Fichier'),

                Tables\Columns\TextColumn::make('duration')
                    ->label("Durée")
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('resolution')
                    ->label("Résolution")
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('subtitle_file')
                    ->label("Sous-titres")
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn ($record) => !empty($record->subtitle_file)),

                Tables\Columns\IconColumn::make('audiodescription_file')
                    ->label("Audio-desc")
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn ($record) => !empty($record->audiodescription_file)),

                Tables\Columns\ToggleColumn::make('status')
                    ->label("Active"),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Créée le")
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'youtube' => 'YouTube',
                        'file' => 'Fichier uploadé',
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active'),
            ])
            ->actions([
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
