@extends('layouts.app')

@section('title', 'Video — Uzbekistan')

@section('content')

    <div class="chips-container">
        <div class="chips-scroll" id="chipsScroll">
            <a href="{{ route('home', array_filter(['tag' => $activeTag, 'search' => $search])) }}"
               class="chip {{ !$activeCategory ? 'active' : '' }}">
                Hammasi
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('home', array_filter(['category' => $cat->slug, 'tag' => $activeTag, 'search' => $search])) }}"
                   class="chip {{ $activeCategory === $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    @if($activeCategory || $activeTag || $search)
        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
            @if($activeCategory)
                @php($cat = $categories->firstWhere('slug', $activeCategory))
                <span class="badge" style="background: {{ $cat?->color ?? '#666' }}; font-size:13px; padding:6px 10px">
                    {{ $cat?->name ?? $activeCategory }}
                    <a href="{{ route('home', array_filter(['tag' => $activeTag, 'search' => $search])) }}" style="color:inherit;margin-left:6px">✕</a>
                </span>
            @endif
            @if($activeTag)
                <span class="badge bg-secondary" style="font-size:13px; padding:6px 10px">
                    #{{ $activeTag }}
                    <a href="{{ route('home', array_filter(['category' => $activeCategory, 'search' => $search])) }}" style="color:inherit;margin-left:6px">✕</a>
                </span>
            @endif
            @if($search)
                <span class="badge bg-secondary" style="font-size:13px; padding:6px 10px">
                    🔍 "{{ $search }}"
                    <a href="{{ route('home', array_filter(['category' => $activeCategory, 'tag' => $activeTag])) }}" style="color:inherit;margin-left:6px">✕</a>
                </span>
            @endif
            <small class="text-secondary">{{ $videos->total() }} ta natija</small>
        </div>
    @endif

    @if($videos->isEmpty())
        <div style="text-align:center; padding:60px 0; color:var(--text-secondary)">
            <i class="fas fa-film" style="font-size:48px; margin-bottom:16px; display:block"></i>
            <p>Videolar topilmadi.</p>
            <a href="{{ route('home') }}" class="chip" style="display:inline-block; margin-top:8px">Hammasini ko'rish</a>
        </div>
    @else
        <div class="videos-grid" id="videosGrid">
            @foreach($videos as $video)
                <a class="video-card" href="{{ route('videos.show', $video) }}" style="display:block">
                    <div class="thumbnail-wrapper">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy">
                        <span class="duration-badge">{{ $video->duration_formatted }}</span>
                        <div class="play-overlay"><i class="fas fa-play"></i></div>
                    </div>
                    <div class="card-info d-flex gap-2 mt-2">
                        <img src="{{ $video->author_avatar_url }}" class="channel-avatar rounded-circle" width="36" height="36" alt="{{ $video->author_name }}" loading="lazy">
                        <div class="video-meta">
                            <h6 class="video-title">{{ $video->title }}</h6>
                            <p class="channel-name">{{ $video->author_name }}</p>
                            <p class="video-stats">{{ $video->views_formatted }} ko'rishlar • {{ $video->published_at?->diffForHumans() }}</p>
                            @if($video->tags->isNotEmpty())
                                <div class="mt-1">
                                    @foreach($video->tags as $tag)
                                        <span style="font-size:11px; color: {{ $tag->color }}; margin-right:6px">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <button type="button" class="more-btn ms-auto"><i class="fas fa-ellipsis-v"></i></button>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $videos->links() }}
        </div>
    @endif

@endsection
