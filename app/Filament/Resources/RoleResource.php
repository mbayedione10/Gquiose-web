<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Role::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Rôles';

    protected static ?string $navigationGroup = 'Gestion des utilisateurs';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 102;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 0])->schema([
                    TextInput::make('name')
                        ->label('Nom du rôle')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique('roles', 'name', fn (?Model $record) => $record)
                        ->placeholder('Ex: Super Admin')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 6,
                            'lg' => 6,
                        ]),

                    Toggle::make('status')
                        ->label('Actif')
                        ->rules(['boolean'])
                        ->required()
                        ->default(true)
                        ->columnSpan([
                            'default' => 12,
                            'md' => 6,
                            'lg' => 6,
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->placeholder('Décrivez les responsabilités et permissions de ce rôle')
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
                    ->label('Nom')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->toggleable()
                    ->searchable()
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->toggleable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->toggleable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Statut')
                    ->toggleable()
                    ->boolean(),
            ])
            ->filters([DateRangeFilter::make('created_at')]);
    }

    public static function getRelations(): array
    {
        return [
            RoleResource\RelationManagers\UsersRelationManager::class,
            RoleResource\RelationManagers\PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
