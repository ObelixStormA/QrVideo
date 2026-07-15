<aside class="yt-sidebar" id="sidebar">
  <div class="sidebar-section">
    <a href="{{ route('home') }}"
       class="nav-item {{ empty($activeCategory ?? null) && empty($activeTag ?? null) && empty($search ?? null) ? 'active' : '' }}">
      <i class="fas fa-home"></i><span class="nav-label">Bosh sahifa</span>
    </a>
  </div>

  <div class="sidebar-section">
    <div class="section-title">Tarixiy shaxslar</div>

    @foreach($categories as $cat)
      <a href="{{ route('home', ['category' => $cat->slug]) }}"
         class="nav-item {{ ($activeCategory ?? null) === $cat->slug ? 'active' : '' }}">
        <i class="{{ $cat->icon }}" style="color: {{ $cat->color }}"></i>
        <span class="nav-label">{{ $cat->name }}</span>
        <span class="ms-auto" style="font-size:11px;color:var(--text-secondary)">{{ $cat->videos_count }}</span>
      </a>
    @endforeach
  </div>

  @if($channels->isNotEmpty())
    <div class="sidebar-section">
      <div class="section-title">Obunalar</div>

      @foreach($channels as $channel)
        <div class="nav-item">
          <i class="fas fa-user-circle"></i>
          <span class="nav-label">{{ $channel->author_name }}</span>
        </div>
      @endforeach
    </div>
  @endif
</aside>
