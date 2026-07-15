<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->maxLength(100)
                    ->prefix('#')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, Set $set): void {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                ColorPicker::make('color')
                    ->label('Rang')
                    ->default('#10B981'),

                TextInput::make('sort_order')
                    ->label('Tartib')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Faol')
                    ->default(true),
            ]);
    }
}
