<nav class="mobile-bottom-nav d-flex">
  <a class="mobile-nav-item {{ empty($activeCategory ?? null) && empty($activeTag ?? null) ? 'active' : '' }}" href="{{ route('home') }}">
    <i class="fas fa-home"></i><span>Bosh sahifa</span>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-film"></i><span>Shorts</span>
  </a>
  <a class="mobile-nav-item">
    <div class="create-icon">+</div>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-tv"></i><span>Obunalar</span>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-photo-film"></i><span>Kutubxona</span>
  </a>
</nav>
