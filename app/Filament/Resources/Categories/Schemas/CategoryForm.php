<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Asosiy ma'lumot")
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nomi')
                            ->required()
                            ->maxLength(150)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Set $set): void {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(150),

                        Textarea::make('description')
                            ->label('Tavsif')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make("Ko'rinish")
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Rasm')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->directory('categories')
                            ->maxSize(2048),

                        TextInput::make('icon')
                            ->label('Font Awesome ikonka')
                            ->default('fas fa-user')
                            ->placeholder('fas fa-user'),

                        ColorPicker::make('color')
                            ->label('Rang')
                            ->default('#8B5CF6'),

                        TextInput::make('sort_order')
                            ->label('Tartib')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Faol')
                            ->default(true),
                    ]),
            ]);
    }
}
