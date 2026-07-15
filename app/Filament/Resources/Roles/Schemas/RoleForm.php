<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    // Gate::before in AppServiceProvider bypasses on this exact string.
                    ->disabled(fn (?Role $record): bool => $record?->name === 'Super Admin'),
                TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web')
                    ->required()
                    ->maxLength(255),
                CheckboxList::make('permissions')
                    ->label('Ruxsatlar')
                    ->relationship('permissions', 'name')
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
