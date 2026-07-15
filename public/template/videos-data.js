const videosData = [
  {
    id: 1, category: 'education',
    thumbnail: 'https://picsum.photos/seed/v1/480/270',
    title: "JavaScript — To'liq Kurs Yangi Boshlovchilar Uchun 2026",
    channel: 'Dasturlash Akademiyasi',
    avatar: 'https://picsum.photos/seed/ch1/80/80',
    views: 1250000, date: new Date(Date.now() - 3 * 86400000),
    duration: '4:32:15', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
    description: "Ushbu darsda JavaScript dasturlash tilining asoslaridan boshlab, zamonaviy imkoniyatlarigacha bo'lgan barcha mavzular ko'rib chiqiladi."
  },
  {
    id: 2, category: 'music',
    thumbnail: 'https://picsum.photos/seed/v2/480/270',
    title: "Yozgi Hit Qo'shiqlar To'plami 2026 — Eng Yaxshi Treklar",
    channel: 'Music Uzbekistan',
    avatar: 'https://picsum.photos/seed/ch2/80/80',
    views: 3400000, date: new Date(Date.now() - 8 * 3600000),
    duration: '1:02:47', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
    description: "2026-yilning eng mashhur yozgi qo'shiqlaridan iborat maxsus to'plam."
  },
  {
    id: 3, category: 'gaming',
    thumbnail: 'https://picsum.photos/seed/v3/480/270',
    title: "GTA 6 — Birinchi Gameplay Sharhi va Tahlili",
    channel: 'GameZone UZ',
    avatar: 'https://picsum.photos/seed/ch3/80/80',
    views: 892000, date: new Date(Date.now() - 5 * 86400000),
    duration: '18:24', verified: false,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
    description: "GTA 6 o'yinining birinchi gameplay videosi bo'yicha to'liq tahlil va fikrlar."
  },
  {
    id: 4, category: 'food',
    thumbnail: 'https://picsum.photos/seed/v4/480/270',
    title: "Uy Sharoitida Osh Tayyorlash — Oson Retsept",
    channel: 'Oshxona Sirlari',
    avatar: 'https://picsum.photos/seed/ch4/80/80',
    views: 456000, date: new Date(Date.now() - 2 * 86400000),
    duration: '12:10', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
    description: "Milliy taomimiz osh tayyorlashning eng oson va mazali retsepti."
  },
  {
    id: 5, category: 'tech',
    thumbnail: 'https://picsum.photos/seed/v5/480/270',
    title: "iPhone 17 Pro — To'liq Sharh, Kamera Testi va Narxi",
    channel: 'Tech Review UZ',
    avatar: 'https://picsum.photos/seed/ch5/80/80',
    views: 2100000, date: new Date(Date.now() - 30 * 3600000),
    duration: '22:05', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
    description: "iPhone 17 Pro haqida to'liq sharh: dizayn, unumdorlik, kamera testlari va narxi."
  },
  {
    id: 6, category: 'sport',
    thumbnail: 'https://picsum.photos/seed/v6/480/270',
    title: "O'zbekiston Milliy Terma Jamoasi — Eng Yaxshi Gollar",
    channel: 'Sport Olami',
    avatar: 'https://picsum.photos/seed/ch6/80/80',
    views: 678000, date: new Date(Date.now() - 6 * 86400000),
    duration: '9:47', verified: false,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
    description: "O'zbekiston milliy terma jamoasining so'nggi mavsumdagi eng yaxshi gollari to'plami."
  },
  {
    id: 7, category: 'news',
    thumbnail: 'https://picsum.photos/seed/v7/480/270',
    title: "Bugungi Asosiy Yangiliklar — Qisqacha Sharh",
    channel: 'Yangiliklar Kanali',
    avatar: 'https://picsum.photos/seed/ch7/80/80',
    views: 145000, date: new Date(Date.now() - 4 * 3600000),
    duration: '15:32', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
    description: "Bugungi kunning eng muhim yangiliklari qisqacha sharh shaklida."
  },
  {
    id: 8, category: 'comedy',
    thumbnail: 'https://picsum.photos/seed/v8/480/270',
    title: "Kulgili Videolar To'plami — Kulmasdan Iloji Yo'q!",
    channel: 'Hazil Studio',
    avatar: 'https://picsum.photos/seed/ch8/80/80',
    views: 5600000, date: new Date(Date.now() - 12 * 86400000),
    duration: '10:58', verified: false,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
    description: "Eng kulgili lahzalardan iborat maxsus to'plam — kulmasdan iloji yo'q!"
  },
  {
    id: 9, category: 'live',
    thumbnail: 'https://picsum.photos/seed/v9/480/270',
    title: "Jonli Efir: Dasturlash Bo'yicha Savol-Javob",
    channel: 'Dasturlash Akademiyasi',
    avatar: 'https://picsum.photos/seed/ch1/80/80',
    views: 23000, date: new Date(Date.now() - 45 * 60000),
    duration: 'JONLI', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
    description: "Dasturlash bo'yicha jonli efirda savol-javob sessiyasi."
  },
  {
    id: 10, category: 'education',
    thumbnail: 'https://picsum.photos/seed/v10/480/270',
    title: "Python bilan Sun'iy Intellekt — Amaliy Loyiha",
    channel: 'Dasturlash Akademiyasi',
    avatar: 'https://picsum.photos/seed/ch1/80/80',
    views: 987000, date: new Date(Date.now() - 20 * 86400000),
    duration: '35:12', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
    description: "Python tilida sun'iy intellekt asosida amaliy loyiha yaratish darsi."
  },
  {
    id: 11, category: 'tech',
    thumbnail: 'https://picsum.photos/seed/v11/480/270',
    title: "Eng Yaxshi Noutbuklar 2026 — Xarid Qilishdan Oldin Ko'ring",
    channel: 'Tech Review UZ',
    avatar: 'https://picsum.photos/seed/ch5/80/80',
    views: 334000, date: new Date(Date.now() - 9 * 86400000),
    duration: '14:40', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
    description: "2026-yilning eng yaxshi noutbuklari sharhi va xarid qilishdan oldingi tavsiyalar."
  },
  {
    id: 12, category: 'music',
    thumbnail: 'https://picsum.photos/seed/v12/480/270',
    title: "Akustik Kontsert — To'liq Ijro",
    channel: 'Music Uzbekistan',
    avatar: 'https://picsum.photos/seed/ch2/80/80',
    views: 156000, date: new Date(Date.now() - 60 * 60000),
    duration: '48:33', verified: true,
    videoUrl: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',
    description: "Akustik kontsertning to'liq ijrosi — jonli tovush bilan."
  }
];
