# YouTube Platform Template — Claude Skill

> **Maqsad:** Ushbu skill Claude'ga HTML, CSS, JavaScript, Bootstrap va boshqa texnologiyalardan foydalanib, to'liq funksional YouTube-uslubdagi video platforma shablonini yaratishni o'rgatadi.

---

## 📋 Skill haqida

| Maydon | Qiymat |
|---|---|
| **Nom** | `youtube-platform-template` |
| **Versiya** | `1.0.0` |
| **Texnologiyalar** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, Font Awesome, Google Fonts |
| **Platforma** | Brauzer (responsive: mobil, planshet, desktop) |
| **Foydalanish** | Claude Artifact yoki mustaqil `.html` fayl |

---

## 🎯 Skill ishga tushiruvchi kalit so'zlar (Triggers)

Quyidagi so'rovlar ushbu skillni faollashtiradi:

- "YouTube kabi video platforma template yasab ber"
- "video sharing website shablon kerak"
- "YouTube clone HTML CSS bilan"
- "video platformasi UI dizayni"
- "bootstrap bilan video sayt template"

---

## 🏗️ Arxitektura va Tuzilma

### Sahifa komponentlari

```
YouTube Template
│
├── 🔝 NAVBAR (Header)
│   ├── Logo (hamburger + YouTube logo)
│   ├── Search bar (mic tugmasi bilan)
│   └── Right icons (video upload, notification, avatar)
│
├── 📌 SIDEBAR (Desktop)
│   ├── Navigation links (Home, Shorts, Subscriptions...)
│   ├── Explore section (Trending, Music, Gaming...)
│   └── Subscriptions list
│
├── 🎬 MAIN CONTENT
│   ├── Category filter chips (All, Music, Gaming, News...)
│   └── Video Grid (responsive cards)
│       ├── Thumbnail (hover play overlay)
│       ├── Channel avatar
│       ├── Video title
│       ├── Channel name
│       └── Views + Upload time
│
└── 📱 MOBILE BOTTOM NAV
    └── (Home, Shorts, +, Subscriptions, Library)
```

---

## 💡 Dizayn Tizimi (Design Tokens)

### Rang palitasi

```css
:root {
  /* YouTube asosiy ranglar */
  --yt-red:        #FF0000;
  --yt-red-dark:   #CC0000;
  --yt-red-hover:  #ff4444;

  /* Fon ranglar (Dark mode) */
  --bg-primary:    #0f0f0f;
  --bg-secondary:  #181818;
  --bg-hover:      #272727;
  --bg-chip:       #272727;
  --bg-chip-active:#f1f1f1;

  /* Matn ranglar */
  --text-primary:  #f1f1f1;
  --text-secondary:#aaaaaa;
  --text-chip-active: #0f0f0f;

  /* UI elementlar */
  --border-color:  #3f3f3f;
  --sidebar-width: 240px;
  --sidebar-mini:  72px;
  --navbar-height: 56px;
  --border-radius-sm: 8px;
  --border-radius-md: 12px;
  --border-radius-lg: 20px;
  --border-radius-full: 50px;
}
```

### Tipografiya

```css
/* Google Fonts import */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

body {
  font-family: 'Roboto', 'Arial', sans-serif;
}

/* Tipografiya shkalasi */
/* Display  — 20px / 700 — Video sarlavhalari */
/* Body     — 14px / 400 — Umumiy matn       */
/* Caption  — 12px / 400 — Metadata (views)  */
/* Micro    — 11px / 400 — Badges, chips      */
```

---

## 📦 CDN Bog'liqliklar

```html
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome 6 (ikonlar) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Google Fonts: Roboto -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

---

## 🧩 Komponent Spetsifikatsiyalari

### 1. NAVBAR

```html
<!-- Navbar tuzilmasi -->
<nav class="yt-navbar">
  <!-- Chap: Hamburger + Logo -->
  <div class="navbar-left">
    <button class="hamburger-btn">☰</button>
    <a class="navbar-brand">
      <i class="fab fa-youtube" style="color:#FF0000"></i>
      <span>YouTube</span>
      <sup class="country-badge">UZ</sup>  <!-- ixtiyoriy -->
    </a>
  </div>

  <!-- O'rta: Search -->
  <div class="navbar-center">
    <div class="search-container">
      <input type="search" placeholder="Qidirish" class="search-input">
      <button class="search-btn"><i class="fas fa-search"></i></button>
      <button class="mic-btn"><i class="fas fa-microphone"></i></button>
    </div>
  </div>

  <!-- O'ng: Actions -->
  <div class="navbar-right">
    <button class="icon-btn" title="Video yuklash">
      <i class="fas fa-video"></i>
    </button>
    <button class="icon-btn" title="Ilovalar">
      <i class="fas fa-th"></i>
    </button>
    <button class="icon-btn notification-btn" title="Bildirishnomalar">
      <i class="fas fa-bell"></i>
      <span class="badge">9</span>
    </button>
    <img src="avatar.jpg" class="user-avatar" alt="Profil">
  </div>
</nav>
```

**Navbar CSS xususiyatlari:**
- `position: fixed; top: 0; z-index: 1000`
- Backdrop blur: `backdrop-filter: blur(10px)`
- Balandlik: `56px`
- Search input: `border-radius: 50px 0 0 50px` (pill shaklida)

---

### 2. SIDEBAR

```
Sidebar holatlari:
┌─────────────────────┬──────────────────────────┐
│   KICHIK (72px)     │    KATTA (240px)          │
├─────────────────────┼──────────────────────────┤
│  🏠                 │  🏠  Bosh sahifa          │
│  📱                 │  📱  Shorts               │
│  📺                 │  📺  Obunalar             │
│  📚                 │  📚  Kutubxona            │
│  ─────              │  ──────────────────       │
│  🔥                 │  🔥  Trend                │
│  🛍️                 │  🛍️  Shopping             │
│  🎵                 │  🎵  Musiqa               │
│  🎮                 │  🎮  Gaming               │
│  📰                 │  📰  Yangiliklar          │
└─────────────────────┴──────────────────────────┘
```

**Sidebar CSS:**
- Default: `width: 240px` (desktop ≥992px)
- Yopilgan: `width: 72px` (faqat ikonlar)
- Mobil: `position: fixed; transform: translateX(-100%)` + overlay

---

### 3. VIDEO CARD

```
┌────────────────────────────────────────┐
│  ┌──────────────────────────────────┐  │
│  │         THUMBNAIL                │  │  ← 16:9 nisbat
│  │    [Hover: ▶ play overlay]       │  │
│  │                         [12:34]  │  │  ← Duration badge
│  └──────────────────────────────────┘  │
│  ┌──┐  Video sarlavhasi (2 qator)      │
│  │  │  max 2 qator, ellipsis           │
│  │🧑│  Kanal nomi  ✓                   │
│  └──┘  1.2M ko'rishlar • 3 kun oldin   │
│        [⋮ kontekst menyu]              │
└────────────────────────────────────────┘
```

**Muhim CSS xususiyatlar:**

```css
.video-card {
  border-radius: 0; /* YouTube stilida card border yo'q */
  background: transparent;
  cursor: pointer;
}

.thumbnail-wrapper {
  position: relative;
  padding-top: 56.25%; /* 16:9 aspect ratio */
  overflow: hidden;
  border-radius: 12px;
}

.thumbnail-wrapper img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s ease;
}

.video-card:hover .thumbnail-wrapper img {
  transform: scale(1.05);
}

.duration-badge {
  position: absolute;
  bottom: 4px;
  right: 4px;
  background: rgba(0,0,0,0.8);
  color: #fff;
  font-size: 11px;
  font-weight: 500;
  padding: 2px 4px;
  border-radius: 4px;
}
```

---

### 4. CATEGORY CHIPS (Filter bar)

```html
<div class="chips-container">
  <div class="chips-scroll">
    <button class="chip active">Hammasi</button>
    <button class="chip">Musiqa</button>
    <button class="chip">Gaming</button>
    <button class="chip">Yangiliklar</button>
    <button class="chip">Jonli efir</button>
    <button class="chip">Sport</button>
    <button class="chip">Ta'lim</button>
    <button class="chip">Texnologiya</button>
    <button class="chip">Komediya</button>
    <button class="chip">Oziq-ovqat</button>
  </div>
</div>
```

```css
.chips-container {
  position: sticky;
  top: 56px; /* navbar height */
  z-index: 100;
  background: var(--bg-primary);
  padding: 12px 0;
}

.chips-scroll {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none;
}

.chips-scroll::-webkit-scrollbar { display: none; }

.chip {
  flex-shrink: 0;
  padding: 6px 12px;
  border-radius: 8px;
  border: none;
  background: var(--bg-chip);
  color: var(--text-primary);
  font-size: 14px;
  cursor: pointer;
  transition: background 0.15s;
}

.chip.active {
  background: var(--bg-chip-active);
  color: var(--text-chip-active);
}
```

---

### 5. RESPONSIVE GRID

```css
/* Video grid - CSS Grid yondashuvi */
.videos-grid {
  display: grid;
  gap: 16px;

  /* 320px → 479px: 1 ustun */
  grid-template-columns: 1fr;
}

/* 480px → 767px: 2 ustun */
@media (min-width: 480px) {
  .videos-grid { grid-template-columns: repeat(2, 1fr); }
}

/* 768px → 1023px: 3 ustun */
@media (min-width: 768px) {
  .videos-grid { grid-template-columns: repeat(3, 1fr); }
}

/* 1024px → 1279px: 3 ustun (sidebar bilan) */
@media (min-width: 1024px) {
  .videos-grid { grid-template-columns: repeat(3, 1fr); }
}

/* 1280px+: 4 ustun */
@media (min-width: 1280px) {
  .videos-grid { grid-template-columns: repeat(4, 1fr); }
}
```

---

### 6. MOBIL BOTTOM NAV

```html
<!-- Faqat mobil qurilmalarda ko'rinadi (d-lg-none) -->
<nav class="mobile-bottom-nav d-flex d-lg-none">
  <a class="mobile-nav-item active">
    <i class="fas fa-home"></i>
    <span>Bosh sahifa</span>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-film"></i>
    <span>Shorts</span>
  </a>
  <a class="mobile-nav-item create-btn">
    <div class="create-icon">+</div>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-tv"></i>
    <span>Obunalar</span>
  </a>
  <a class="mobile-nav-item">
    <i class="fas fa-book"></i>
    <span>Kutubxona</span>
  </a>
</nav>
```

```css
.mobile-bottom-nav {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-color);
  z-index: 1000;
  justify-content: space-around;
  padding: 8px 0;
  height: 56px;
}
```

---

## ⚙️ JavaScript Funksiyalari

### Majburiy funksiyalar

```javascript
// 1. Sidebar toggle
function toggleSidebar() {
  sidebar.classList.toggle('collapsed');
  // collapsed holatda: width → 72px
}

// 2. Chip (filter) tanlash
document.querySelectorAll('.chip').forEach(chip => {
  chip.addEventListener('click', function() {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    filterVideos(this.dataset.category); // ixtiyoriy
  });
});

// 3. Ko'rishlar sonini formatlash
function formatViews(count) {
  if (count >= 1_000_000) return (count / 1_000_000).toFixed(1) + 'M';
  if (count >= 1_000)     return (count / 1_000).toFixed(1) + 'K';
  return count.toString();
}

// 4. Vaqtni nisbiy formatlash
function timeAgo(date) {
  const seconds = Math.floor((new Date() - date) / 1000);
  const intervals = [
    [31536000, 'yil'], [2592000, 'oy'],
    [86400, 'kun'],    [3600, 'soat'],
    [60, 'daqiqa'],   [1, 'soniya']
  ];
  for (const [secs, label] of intervals) {
    const count = Math.floor(seconds / secs);
    if (count >= 1) return `${count} ${label} oldin`;
  }
  return 'Hozir';
}

// 5. Video ma'lumotlari (Mock data)
const videosData = [
  {
    id: 1,
    thumbnail: 'https://picsum.photos/seed/v1/480/270',
    title: 'JavaScript — To\'liq Kurs Yangi Boshlovchilar Uchun 2024',
    channel: 'Dasturlash Akademiyasi',
    avatar: 'https://picsum.photos/seed/ch1/40/40',
    views: 1250000,
    date: new Date(Date.now() - 3 * 24 * 3600 * 1000),
    duration: '4:32:15',
    verified: true
  },
  // ... boshqa videolar
];

// 6. Video cardlarni dinamik render qilish
function renderVideos(videos) {
  const grid = document.querySelector('.videos-grid');
  grid.innerHTML = videos.map(v => `
    <div class="video-card" data-id="${v.id}">
      <div class="thumbnail-wrapper">
        <img src="${v.thumbnail}" alt="${v.title}" loading="lazy">
        <span class="duration-badge">${v.duration}</span>
        <div class="play-overlay">
          <i class="fas fa-play"></i>
        </div>
      </div>
      <div class="card-info d-flex gap-2 mt-2">
        <img src="${v.avatar}" class="channel-avatar rounded-circle" width="36" height="36" alt="${v.channel}">
        <div class="video-meta">
          <h6 class="video-title">${v.title}</h6>
          <p class="channel-name text-secondary">
            ${v.channel}
            ${v.verified ? '<i class="fas fa-check-circle text-secondary ms-1" style="font-size:11px"></i>' : ''}
          </p>
          <p class="video-stats text-secondary">
            ${formatViews(v.views)} ko'rishlar •
            ${timeAgo(v.date)}
          </p>
        </div>
        <button class="more-btn ms-auto">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
    </div>
  `).join('');
}

// 7. Sidebar overlay (mobil)
function openMobileSidebar() {
  overlay.classList.add('active');
  sidebar.classList.add('mobile-open');
  document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
  overlay.classList.remove('active');
  sidebar.classList.remove('mobile-open');
  document.body.style.overflow = '';
}

// 8. Lazy loading (IntersectionObserver)
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      observer.unobserve(img);
    }
  });
}, { rootMargin: '200px' });

document.querySelectorAll('img[data-src]').forEach(img => observer.observe(img));
```

---

## 📐 Layout Strukturasi (HTML Skeleton)

```html
<!DOCTYPE html>
<html lang="uz" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YouTube — Uzbekistan</title>
  <!-- CDN linklar bu yerda -->
</head>
<body>

  <!-- 1. NAVBAR -->
  <nav class="yt-navbar" id="navbar">...</nav>

  <!-- 2. WRAPPER -->
  <div class="yt-wrapper">

    <!-- 3. SIDEBAR -->
    <aside class="yt-sidebar" id="sidebar">...</aside>

    <!-- 4. OVERLAY (mobil sidebar uchun) -->
    <div class="sidebar-overlay" id="overlay"></div>

    <!-- 5. MAIN CONTENT -->
    <main class="yt-main">

      <!-- 5a. CHIPS -->
      <div class="chips-container">...</div>

      <!-- 5b. VIDEO GRID -->
      <div class="videos-grid" id="videosGrid">...</div>

    </main>

  </div>

  <!-- 6. MOBILE BOTTOM NAV -->
  <nav class="mobile-bottom-nav d-flex d-lg-none">...</nav>

  <!-- Scripts -->
</body>
</html>
```

---

## 🎨 Dark / Light Mode

```javascript
// Theme toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

themeToggle.addEventListener('click', () => {
  const current = html.getAttribute('data-theme');
  html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
  localStorage.setItem('yt-theme', html.getAttribute('data-theme'));
});

// Saqlangan temani yuklash
const saved = localStorage.getItem('yt-theme') || 'dark';
html.setAttribute('data-theme', saved);
```

```css
/* Light mode overrides */
[data-theme="light"] {
  --bg-primary:    #ffffff;
  --bg-secondary:  #f9f9f9;
  --bg-hover:      #e5e5e5;
  --bg-chip:       #f2f2f2;
  --bg-chip-active:#0f0f0f;
  --text-primary:  #0f0f0f;
  --text-secondary:#606060;
  --border-color:  #e5e5e5;
}
```

---

## ✅ Chiqish sifati tekshiruvi (Quality Checklist)

Claude ushbu templateni yaratganda quyidagilarni tekshirishi shart:

### Funksionallik
- [ ] Hamburger tugmasi sidebar ni ochadi/yopadi
- [ ] Chip bosish aktiv holatni almashtiradi
- [ ] Video cardlar to'g'ri render bo'ladi (kamida 8 ta)
- [ ] Hover holatida thumbnail kengayadi
- [ ] Mobil pastki nav ko'rinadi (≤992px)
- [ ] Sidebar mobilda overlay bilan ochiladi

### Responsive
- [ ] 320px — 1 ustun, pastki nav
- [ ] 768px — 3 ustun, sidebar mini
- [ ] 1280px — 4 ustun, to'liq sidebar

### Dizayn
- [ ] Dark mode standart holat
- [ ] Ko'rishlar soni formatlangan (1.2M, 450K)
- [ ] Vaqt nisbiy (3 kun oldin, 2 soat oldin)
- [ ] Thumbnail 16:9 nisbatda
- [ ] Duration badge har bir cardda mavjud

### Texnik
- [ ] Faqat bitta `.html` fayl (CSS + JS ichida)
- [ ] CDN orqali Bootstrap va Font Awesome
- [ ] `loading="lazy"` barcha rasmlarda
- [ ] Konsol da xatolik yo'q

---

## 🚀 Kengaytirish imkoniyatlari (Opsional)

| Xususiyat | Texnologiya | Tavsif |
|---|---|---|
| Video player sahifasi | HTML5 `<video>` | Ichki sahifa, klik bilan ochiladi |
| Qidiruv filtri | JavaScript | Real-time filter by title/channel |
| Infinite scroll | IntersectionObserver | Pastga scroll qilinganda yangi cardlar |
| Notification panel | Bootstrap Offcanvas | Bell ikoniga bosganda |
| Context menu | Custom CSS menu | ⋮ (uch nuqta) bosganda |
| Claude AI Artifact | Anthropic API | Claude API bilan AI-powered search |

---

## 📝 Claude uchun namuna prompt

Foydalanuvchi quyidagi so'rovni berganda ushbu skillni to'liq qo'llash kerak:

```
"YouTube uslubidagi video platforma template yasab ber. 
Dark mode, responsive, sidebar, search bar, video cardlar 
va mobil navigatsiya bo'lsin. Suyuq Uzbekcha interfeys."
```

**Claude chiqarishi kerak bo'lgan narsa:**
- Bitta `.html` artifact (≥ 300 satr)
- Kamida 8 ta mock video card
- To'liq dark mode
- Bootstrap 5 grid + Font Awesome ikonlar
- Ishlayotgan hamburger + chip toggle
- Barcha sahifaga shar holda responsive layout

---

*Skill versiyasi: 1.0.0 | Muallif: Olim uchun tayyorlandi | Sana: 2026*
