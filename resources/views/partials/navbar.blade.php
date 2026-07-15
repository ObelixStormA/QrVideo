<nav class="yt-navbar" id="navbar">
  <div class="navbar-left">
    <button class="hamburger-btn" id="hamburgerBtn" title="Menyu"><i class="fas fa-bars"></i></button>
    <a class="navbar-brand" href="{{ route('home') }}">
      <img src="{{ asset('template/1.png') }}" alt="Logo">
      <span>Video</span>
      <sup class="country-badge">UZ</sup>
    </a>
  </div>

  <div class="navbar-center">
    <form method="GET" action="{{ route('home') }}" class="search-container">
      @if(!empty($activeCategory))
        <input type="hidden" name="category" value="{{ $activeCategory }}">
      @endif
      @if(!empty($activeTag))
        <input type="hidden" name="tag" value="{{ $activeTag }}">
      @endif
      <input type="search" name="search" placeholder="Qidirish" class="search-input" id="searchInput" value="{{ $search ?? '' }}">
      <button type="submit" class="search-btn" title="Qidirish"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="navbar-right">
    <button class="icon-btn" id="themeToggle" title="Mavzu almashtirish"><i class="fas fa-circle-half-stroke"></i></button>
  </div>
</nav>
