<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            if (! Schema::hasColumn('videos', 'ar_uuid')) {
                $table->uuid('ar_uuid')->unique()->nullable()->after('id');
            }

            if (! Schema::hasColumn('videos', 'marker_image_path')) {
                $table->string('marker_image_path', 500)->nullable()->after('video_url');
            }

            if (! Schema::hasColumn('videos', 'mind_file_path')) {
                $table->string('mind_file_path', 500)->nullable()->after('marker_image_path');
            }

            if (! Schema::hasColumn('videos', 'ar_enabled')) {
                $table->boolean('ar_enabled')->default(false)->after('mind_file_path');
            }

            if (! Schema::hasColumn('videos', 'mind_compile_status')) {
                $table->enum('mind_compile_status', ['pending', 'processing', 'ready', 'failed'])
                    ->default('pending')
                    ->after('ar_enabled');
            }

            if (! Schema::hasColumn('videos', 'mind_compile_error')) {
                $table->text('mind_compile_error')->nullable()->after('mind_compile_status');
            }
        });

        DB::table('videos')->whereNull('ar_uuid')->select('id')->orderBy('id')->cursor()->each(
            fn (object $video) => DB::table('videos')->where('id', $video->id)->update(['ar_uuid' => (string) Str::uuid()])
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            $table->dropColumn([
                'ar_uuid',
                'marker_image_path',
                'mind_file_path',
                'ar_enabled',
                'mind_compile_status',
                'mind_compile_error',
            ]);
        });
    }
};
