<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminInvitationResource\Pages;
use App\Mail\AdminInvitationMail;
use App\Models\AdminInvitation;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class AdminInvitationResource extends Resource
{
    protected static ?string $model = AdminInvitation::class;

    protected static ?string $recordTitleAttribute = 'email';

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $navigationGroup = 'Gestion des utilisateurs';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 101;

    protected static ?string $modelLabel = 'Invitation';

    protected static ?string $pluralModelLabel = 'Invitations';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Inviter un nouvel administrateur')
                ->description('Un email sera envoyé à cette adresse avec un lien pour activer le compte.')
                ->schema([
                    Grid::make(['default' => 0])->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->rules(['max:255', 'string'])
                            ->required()
                            ->placeholder('Nom de l\'invité')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 6,
                            ]),

                        TextInput::make('email')
                            ->label('Email')
                            ->rules(['email'])
                            ->required()
                            ->unique('admin_invitations', 'email')
                            ->unique('users', 'email')
                            ->email()
                            ->placeholder('email@exemple.com')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 6,
                            ]),

                        Select::make('role_id')
                            ->label('Rôle')
                            ->rules(['exists:roles,id'])
                            ->required()
                            ->relationship('role', 'name')
                            ->searchable()
                            ->placeholder('Sélectionner un rôle')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
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
                    ->label('Nom')
                    ->toggleable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->toggleable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rôle')
                    ->toggleable()
                    ->badge()
                    ->limit(50),
                Tables\Columns\TextColumn::make('invitedBy.name')
                    ->label('Invité par')
                    ->toggleable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(function (AdminInvitation $record): string {
                        if ($record->isAccepted()) {
                            return 'Acceptée';
                        }
                        if ($record->isExpired()) {
                            return 'Expirée';
                        }

                        return 'En attente';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Acceptée' => 'success',
                        'Expirée' => 'danger',
                        'En attente' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'accepted' => 'Acceptée',
                        'expired' => 'Expirée',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value']) {
                            'pending' => $query->whereNull('accepted_at')->where('expires_at', '>', now()),
                            'accepted' => $query->whereNotNull('accepted_at'),
                            'expired' => $query->whereNull('accepted_at')->where('expires_at', '<=', now()),
                            default => $query,
                        };
                    }),
                SelectFilter::make('role_id')
                    ->relationship('role', 'name')
                    ->label('Rôle'),
            ])
            ->actions([
                Action::make('resend')
                    ->label('Renvoyer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Renvoyer l\'invitation')
                    ->modalDescription('Voulez-vous renvoyer l\'email d\'invitation ? Le lien précédent restera valide.')
                    ->visible(fn (AdminInvitation $record): bool => $record->isPending())
                    ->action(function (AdminInvitation $record) {
                        Mail::to($record->email)->send(new AdminInvitationMail($record));

                        Notification::make()
                            ->title('Invitation renvoyée')
                            ->body('L\'email d\'invitation a été renvoyé à '.$record->email)
                            ->success()
                            ->send();
                    }),
                Action::make('extend')
                    ->label('Prolonger')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Prolonger l\'invitation')
                    ->modalDescription('Prolonger la validité de l\'invitation de 48 heures supplémentaires ?')
                    ->visible(fn (AdminInvitation $record): bool => ! $record->isAccepted())
                    ->action(function (AdminInvitation $record) {
                        $record->update([
                            'expires_at' => now()->addHours(48),
                            'token' => AdminInvitation::generateToken(),
                        ]);

                        Mail::to($record->email)->send(new AdminInvitationMail($record));

                        Notification::make()
                            ->title('Invitation prolongée')
                            ->body('L\'invitation a été prolongée et un nouvel email a été envoyé.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Supprimer'),
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
            'index' => Pages\ListAdminInvitations::route('/'),
            'create' => Pages\CreateAdminInvitation::route('/create'),
        ];
    }
}
