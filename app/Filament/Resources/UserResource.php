<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class UserResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Administrateurs';

    protected static ?string $navigationGroup = 'Gestion des utilisateurs';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 0])->schema([
                    TextInput::make('name')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Name')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('phone')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(
                            'users',
                            'phone',
                            fn (?Model $record) => $record
                        )
                        ->placeholder('Phone')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('email')
                        ->rules(['email'])
                        ->required()
                        ->unique(
                            'users',
                            'email',
                            fn (?Model $record) => $record
                        )
                        ->email()
                        ->placeholder('Email')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('password')
                        ->required()
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                        ->required(
                            fn (Component $livewire) => $livewire instanceof Pages\CreateUser
                        )
                        ->placeholder('Password')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Select::make('role_id')
                        ->rules(['exists:roles,id'])
                        ->required()
                        ->relationship('role', 'name')
                        ->searchable()
                        ->placeholder('Role')
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
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('email')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('role.name')
                    ->toggleable()
                    ->limit(50),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),

                SelectFilter::make('role_id')
                    ->relationship('role', 'name')
                    ->indicator('Role')
                    ->multiple()
                    ->label('Role'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('changePassword')
                        ->label('Modifier le mot de passe')
                        ->icon('heroicon-o-key')
                        ->form([
                            TextInput::make('new_password')
                                ->label('Nouveau mot de passe')
                                ->password()
                                ->required()
                                ->minLength(6)
                                ->maxLength(255),
                            TextInput::make('new_password_confirmation')
                                ->label('Confirmer le mot de passe')
                                ->password()
                                ->required()
                                ->same('new_password')
                                ->minLength(6)
                                ->maxLength(255),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'password' => \Hash::make($data['new_password']),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Mot de passe modifié')
                                ->body('Le mot de passe a été mis à jour avec succès.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Modifier le mot de passe')
                        ->modalDescription('Veuillez saisir le nouveau mot de passe pour cet administrateur.')
                        ->modalSubmitActionLabel('Modifier'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer')
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer l\'administrateur')
                        ->modalDescription('Êtes-vous sûr de vouloir supprimer cet administrateur ? Cette action est irréversible.')
                        ->modalSubmitActionLabel('Oui, supprimer')
                        ->successNotificationTitle('Administrateur supprimé avec succès'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer les administrateurs sélectionnés')
                        ->modalDescription('Êtes-vous sûr de vouloir supprimer ces administrateurs ? Cette action est irréversible.')
                        ->modalSubmitActionLabel('Oui, supprimer')
                        ->successNotificationTitle('Administrateurs supprimés avec succès'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [UserResource\RelationManagers\ArticlesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
