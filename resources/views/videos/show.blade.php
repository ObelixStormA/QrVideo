@extends('layouts.app')

@section('title', $video->title . ' — Video — Uzbekistan')

@push('styles')
<style>
.watch-main {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.player-wrapper {
  position: relative;
  width: 100%;
  padding-top: 56.25%;
  background: #000;
  border-radius: var(--border-radius-md);
  overflow: hidden;
}
.player-wrapper video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background: #000;
}

.watch-title {
  font-size: 20px;
  font-weight: 600;
  margin: 16px 0 12px;
  line-height: 1.35;
}

.watch-description {
  background: var(--bg-chip);
  border-radius: var(--border-radius-md);
  padding: 12px 16px;
  font-size: 14px;
  line-height: 1.5;
}
.watch-description .desc-stats {
  font-weight: 500;
  margin-bottom: 6px;
}
.watch-description .desc-text {
  color: var(--text-primary);
}

@media (max-width: 991.98px) {
  .watch-main { padding: 12px 16px 72px; }
}
</style>
@endpush

@section('content')
    <div class="watch-main">
        <div class="player-wrapper">
            @if($video->video_url_resolved)
                <video id="videoPlayer" controls autoplay poster="{{ $video->thumbnail_url }}">
                    <source src="{{ $video->video_url_resolved }}">
                    Brauzeringiz video formatini qo'llab-quvvatlamaydi.
                </video>
            @else
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
            @endif
        </div>

        <h1 class="watch-title">{{ $video->title }}</h1>

        <div class="watch-description">
            <p class="desc-stats">
                {{ $video->views_formatted }} ko'rishlar • {{ $video->published_at?->diffForHumans() }}
                @if($video->category)
                    <span style="color: {{ $video->category->color }}; margin-left:6px">• {{ $video->category->name }}</span>
                @endif
            </p>
            <p class="desc-text">{{ $video->description }}</p>

            @if($video->tags->isNotEmpty())
                <div class="mt-2">
                    @foreach($video->tags as $tag)
                        <span style="font-size:12px; color: {{ $tag->color }}; margin-right:8px">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
