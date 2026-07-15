<?php

declare(strict_types=1);

namespace App\Filament\Resources\Videos\Tables;

use App\Actions\Videos\GenerateArQrCodeAction;
use App\Models\Video;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->width(120)
                    ->height(67)
                    ->extraImgAttributes(['style' => 'border-radius:8px;object-fit:cover']),

                TextColumn::make('title')
                    ->label('Video')
                    ->searchable()
                    ->sortable()
                    ->limit(45)
                    ->description(fn (Video $record): string => ($record->author_name ?? '—').' • '.$record->views_formatted." ko'rishlar"),

                TextColumn::make('category.name')
                    ->label('Kategoriya')
                    ->badge()
                    ->color('purple'),

                TextColumn::make('tags.name')
                    ->label('Teglar')
                    ->badge()
                    ->color('success')
                    ->separator(','),

                TextColumn::make('duration_seconds')
                    ->label('Vaqt')
                    ->formatStateUsing(fn (Video $record): string => $record->duration_formatted),

                TextColumn::make('status')
                    ->label('Holat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Qoralama',
                        'published' => 'Nashr etilgan',
                        'archived' => 'Arxiv',
                        default => $state,
                    }),

                IconColumn::make('is_live')
                    ->label('Jonli')
                    ->boolean(),

                ImageColumn::make('marker_image_path')
                    ->label('Marker')
                    ->width(60)
                    ->height(60)
                    ->extraImgAttributes(['style' => 'border-radius:8px;object-fit:cover']),

                TextColumn::make('mind_compile_status')
                    ->label('.mind holati')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ready' => 'success',
                        'processing' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ready' => 'Tayyor',
                        'processing' => 'Tayyorlanmoqda',
                        'failed' => 'Xato',
                        default => 'Kutilmoqda',
                    })
                    ->tooltip(fn (Video $record): ?string => $record->mind_compile_status === 'failed' ? $record->mind_compile_error : null),

                TextColumn::make('published_at')
                    ->label('Sana')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Kategoriya'),
                SelectFilter::make('status')
                    ->label('Holat')
                    ->options([
                        'draft' => 'Qoralama',
                        'published' => 'Nashr etilgan',
                        'archived' => 'Arxiv',
                    ]),
                TernaryFilter::make('is_live')->label('Jonli efir'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Nashr et')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Video $record): bool => $record->status !== 'published')
                    ->action(fn (Video $record) => $record->update(['status' => 'published', 'published_at' => now()])),

                ActionGroup::make([
                    Action::make('download_qr_png')
                        ->label('QR kod (PNG)')
                        ->icon(Heroicon::OutlinedQrCode)
                        ->action(function (Video $record, GenerateArQrCodeAction $action) {
                            $file = $action->execute($record, 'png');

                            return response()->streamDownload(
                                fn () => print $file->content,
                                $file->filename,
                                ['Content-Type' => $file->mimeType],
                            );
                        }),

                    Action::make('download_qr_svg')
                        ->label('QR kod (SVG)')
                        ->icon(Heroicon::OutlinedQrCode)
                        ->action(function (Video $record, GenerateArQrCodeAction $action) {
                            $file = $action->execute($record, 'svg');

                            return response()->streamDownload(
                                fn () => print $file->content,
                                $file->filename,
                                ['Content-Type' => $file->mimeType],
                            );
                        }),

                    Action::make('open_ar_page')
                        ->label('AR sahifani ochish')
                        ->icon(Heroicon::OutlinedEye)
                        ->url(fn (Video $record): string => route('ar.show', $record->ar_uuid))
                        ->openUrlInNewTab(),
                ])
                    ->label('QR kodni yuklab olish')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->visible(fn (Video $record): bool => auth()->user()?->can('update', $record) ?? false),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish_all')
                        ->label('Tanlanganlarni nashr et')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ])),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
