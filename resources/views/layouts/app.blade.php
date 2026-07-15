<!DOCTYPE html>
<html lang="uz" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Video — UZ')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('template/styles.css') }}">
@stack('styles')
</head>
<body>

@include('partials.navbar')

<div class="yt-wrapper">

    @include('partials.sidebar')

    <div class="sidebar-overlay" id="overlay"></div>

    <main class="yt-main" id="mainContent">
        @yield('content')
    </main>

</div>

@include('partials.mobile-nav')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay = document.getElementById('overlay');
const hamburgerBtn = document.getElementById('hamburgerBtn');

function isMobile() { return window.innerWidth < 992; }

function toggleSidebar() {
  if (isMobile()) {
    const isOpen = sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  } else {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
  }
}

function closeMobileSidebar() {
  overlay.classList.remove('active');
  sidebar.classList.remove('mobile-open');
  document.body.style.overflow = '';
}

hamburgerBtn.addEventListener('click', toggleSidebar);
overlay.addEventListener('click', closeMobileSidebar);

window.addEventListener('resize', () => {
  if (!isMobile()) closeMobileSidebar();
});

const themeToggle = document.getElementById('themeToggle');
const htmlEl = document.documentElement;
themeToggle.addEventListener('click', () => {
  const next = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  htmlEl.setAttribute('data-theme', next);
  localStorage.setItem('yt-theme', next);
});
htmlEl.setAttribute('data-theme', localStorage.getItem('yt-theme') || 'dark');
</script>
@stack('scripts')
</body>
</html>
