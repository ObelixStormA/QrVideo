<?php

declare(strict_types=1);

namespace App\Filament\Resources\Videos\Schemas;

use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make("Video ma'lumotlari")
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Sarlavha')
                                    ->required()
                                    ->maxLength(500)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Set $set): void {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                Select::make('category_id')
                                    ->label('Kategoriya')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('tags')
                                    ->label('Teglar')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                Textarea::make('description')
                                    ->label('Tavsif')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Media')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label('Thumbnail')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(['16:9'])
                                    ->directory('videos/thumbnails')
                                    ->maxSize(5120)
                                    ->helperText('Tavsiya: 1280×720 px'),

                                FileUpload::make('video_url')
                                    ->label('Video fayl')
                                    ->disk('public')
                                    ->directory('videos')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'])
                                    ->maxSize(512000)
                                    ->downloadable()
                                    ->openable()
                                    ->helperText('Maksimal hajm: 500MB. Formatlar: MP4, WebM, MOV, AVI')
                                    ->columnSpanFull(),

                                TextInput::make('duration_seconds')
                                    ->label('Davomiyligi (soniya)')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('soniya')
                                    ->helperText('272 → "4:32"'),
                            ]),

                        Section::make('Muallif')
                            ->columns(2)
                            ->schema([
                                TextInput::make('author_name')
                                    ->label('Muallif nomi')
                                    ->maxLength(255),

                                FileUpload::make('author_avatar')
                                    ->label('Muallif avatari')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(['1:1'])
                                    ->directory('authors'),
                            ]),

                        Section::make('AR sozlamalari')
                            ->description('Jurnalga chop etiladigan marker rasm va video ustiga jonlanish sozlamalari.')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('marker_image_path')
                                    ->label('Marker rasm')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('videos/markers')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->helperText('Jurnalga chop etiladigan rasm. Kontrastli, detallarga boy rasm tanlang — bir xil rangli/tekis rasmlar yomon kuzatiladi. Video qanday nisbatda chiqishini xohlasangiz, rasmni ham shu nisbatda kesib (crop) saqlang — AR video aynan shu rasm nisbatida chiqadi.')
                                    ->columnSpanFull(),

                                FileUpload::make('mind_file_path')
                                    ->label('.mind fayl (qo\'lda, ixtiyoriy)')
                                    ->disk('public')
                                    ->directory('mind-targets')
                                    ->rules([
                                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                            if (! $value) {
                                                return;
                                            }

                                            $extension = is_string($value)
                                                ? pathinfo($value, PATHINFO_EXTENSION)
                                                : $value->getClientOriginalExtension();

                                            if (strtolower((string) $extension) !== 'mind') {
                                                $fail("Fayl kengaytmasi .mind bo'lishi kerak.");
                                            }
                                        },
                                    ])
                                    ->helperText('MindAR veb-kompilyatoridan (hiukim.github.io/mind-ar-js-doc/tools/compile) qo\'lda tayyorlangan fayl. Yuklansa, avtomatik kompilyatsiyani ustidan yozmaydi.')
                                    ->columnSpanFull(),

                                Toggle::make('ar_enabled')
                                    ->label('AR yoqilgan')
                                    ->helperText('Yoqilgach, QR kod orqali ochiladigan AR sahifa faollashadi.')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Nashr holati')
                            ->schema([
                                Select::make('status')
                                    ->label('Holat')
                                    ->options([
                                        'draft' => '📝 Qoralama',
                                        'published' => '✅ Nashr etilgan',
                                        'archived' => '📦 Arxiv',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false),

                                DateTimePicker::make('published_at')
                                    ->label('Nashr vaqti')
                                    ->helperText("Bo'sh = hozir"),
                            ]),

                        Section::make('Sozlamalar')
                            ->schema([
                                Toggle::make('is_live')
                                    ->label('JONLI efir'),
                            ]),

                        Section::make('Statistika')
                            ->schema([
                                TextInput::make('views_count')
                                    ->label("Ko'rishlar")
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
