# Vazifa: Jurnal uchun WebAR — rasm ustida video jonlanishi

## Loyiha konteksti

Bu — Laravel + Filament asosidagi mavjud loyiha. Unda:
- Standart RBAC (rollar va ruxsatlar) allaqachon sozlangan
- `Videos` nomli Filament resource mavjud (video yuklash, `title` va boshqa maydonlar bor)

**Ishni boshlashdan oldin:** `app/Filament/Resources/` ichidagi mavjud `VideoResource` ni, `Video` modelini va migratsiyalarni o'qib chiq. Mavjud maydon nomlariga (masalan `video_path`, `title`) moslash — yangi nom o'ylab topma. Filament versiyasini `composer.json` dan aniqla va o'sha versiya sintaksisidan foydalan.

## Maqsad (foydalanuvchi ssenariysi)

1. Admin Filament panelda video yuklaydi va shu videoga mos **marker rasm** (jurnalga bosiladigan rasm) yuklaydi.
2. Tizim shu yozuv uchun **QR kod** generatsiya qiladi. QR kod va marker rasm jurnalga chop etiladi.
3. O'quvchi telefonida QR kodni skaner qiladi → brauzerda maxsus AR sahifa ochiladi → kamera yoqiladi.
4. Telefon kamerasini jurnal­dagi rasmga qaratganda, **rasm o'rnida video jonlanadi** (rasm ustiga video AR tarzida "yopishadi", rasm qimirlasa video ham birga harakatlanadi).

Hech qanday mobil ilova o'rnatilmaydi — hammasi brauzerda (WebAR).

## Texnologik yechim

- **AR dvijok:** [MindAR.js](https://github.com/hiukim/mind-ar-js) (image tracking) + **A-Frame**. Ikkalasi ham bepul, CDN orqali ulanadi, Chrome (Android) va Safari (iOS) da ishlaydi.
- MindAR rasmni kuzatish uchun marker rasmdan kompilyatsiya qilingan **`.mind` target fayl** talab qiladi.
- **QR kod:** `simplesoftwareio/simple-qrcode` (yoki `endroid/qr-code`) paketi bilan server tomonda SVG/PNG generatsiya.
- **Muhim:** kameraga kirish faqat **HTTPS** da ishlaydi. Lokal test uchun `localhost` yetarli, prodakshenda SSL majburiy.

## Bajariladigan ishlar

### 1. Migratsiya — `videos` jadvalini kengaytirish

Yangi ustunlar (mavjudlariga tegma):

```php
$table->uuid('ar_uuid')->unique()->nullable();      // public AR sahifa uchun ID
$table->string('marker_image_path')->nullable();     // jurnalga bosiladigan rasm
$table->string('mind_file_path')->nullable();        // kompilyatsiya qilingan .mind target
$table->boolean('ar_enabled')->default(false);
```

Model `creating` hodisasida `ar_uuid` avtomatik `Str::uuid()` bilan to'ldirilsin.

### 2. `.mind` target faylni tayyorlash — ikki yo'l (ikkalasini ham qil)

**A) Asosiy yo'l — server tomonda avtomatik kompilyatsiya (agar serverda Node.js bo'lsa):**
- `mind-ar` npm paketining image compiler skriptidan foydalanib, marker rasm yuklanganda Laravel **queue job** (`CompileMindTarget`) orqali `.mind` fayl generatsiya qilinsin.
- Job `Process` facade orqali node skriptni chaqiradi, natijani `storage/app/public/mind-targets/{id}.mind` ga saqlaydi va `mind_file_path` ni yangilaydi.
- Node yoki kompilyatsiya muvaffaqiyatsiz bo'lsa — log yozilsin, yozuv `ar_enabled=false` bo'lib qolsin, Filamentda ogohlantirish ko'rinsin.

**B) Zaxira yo'l — qo'lda yuklash:**
- Filament formada alohida `FileUpload` maydoni: admin [MindAR web kompilyatori](https://hiukim.github.io/mind-ar-js-doc/tools/compile) orqali o'zi kompilyatsiya qilgan `.mind` faylni qo'lda yuklashi mumkin.
- Agar qo'lda yuklangan bo'lsa, avtomatik kompilyatsiya uni ustidan yozmasin.

### 3. Filament `VideoResource` ni kengaytirish

Formaga qo'shiladi (yangi "AR sozlamalari" section ichida):
- `FileUpload::make('marker_image_path')` — faqat rasm (jpg/png), yordam matni: "Jurnalga chop etiladigan rasm. Kontrastli, detallarga boy rasm tanlang — bir xil rangli/tekis rasmlar yomon kuzatiladi."
- `FileUpload::make('mind_file_path')` — ixtiyoriy, `.mind` fayl (zaxira yo'l uchun).
- `Toggle::make('ar_enabled')`.

Jadval (table) ga qo'shiladi:
- Marker rasm preview ustuni.
- `.mind` fayl holati (tayyor/tayyorlanmoqda/xato) — badge ustun.
- **"QR kodni yuklab olish"** action: `route('ar.show', $record->ar_uuid)` manziliga QR kodni **SVG va PNG (600×600, chop uchun yuqori sifat)** ko'rinishida yuklab beradi.
- **"AR sahifani ochish"** action — yangi tabda public sahifani ochadi (test uchun).

RBAC: mavjud permission tizimiga mos ravishda ushbu action'lar `update` ruxsatiga bog'lansin.

### 4. Public AR sahifa (auth talab qilinmaydi)

Route:
```php
Route::get('/ar/{uuid}', ArViewController::class)->name('ar.show');
```

Controller: `ar_uuid` bo'yicha topadi, `ar_enabled=true` va `.mind` fayl mavjudligini tekshiradi, aks holda tushunarli xato sahifa ko'rsatadi.

Blade sahifa (`resources/views/ar/show.blade.php`) — **Filament layoutidan mustaqil, toza sahifa**:

```html
<script src="https://aframe.io/releases/1.5.0/aframe.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.5/dist/mindar-image-aframe.prod.js"></script>

<a-scene mindar-image="imageTargetSrc: {{ $mindUrl }}; autoStart: true"
         color-space="sRGB" renderer="colorManagement: true"
         vr-mode-ui="enabled: false" device-orientation-permission-ui="enabled: false">
  <a-assets>
    <video id="ar-video" src="{{ $videoUrl }}" preload="auto"
           loop muted playsinline webkit-playsinline crossorigin="anonymous"></video>
  </a-assets>
  <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>
  <a-entity mindar-image-target="targetIndex: 0">
    <a-video src="#ar-video" width="1" height="{{ $ratio }}" position="0 0 0"></a-video>
  </a-entity>
</a-scene>
```

JS logikasi:
- `targetFound` hodisasida `video.play()`, `targetLost` da `video.pause()`.
- **iOS Safari cheklovi:** video avtoplay faqat `muted + playsinline` bilan ishlaydi. Sahifada "Ovozni yoqish" tugmasi bo'lsin — birinchi teginishda `video.muted = false; video.play()`.
- Kamera ruxsati so'ralayotganda va target qidirilayotganda o'zbekcha ko'rsatma overlay: "Kamerani jurnaldagi rasmga qarating".
- `width/height` nisbati video o'lchamiga mos hisoblansin (controller'da yoki metadata orqali), video rasm chegarasidan chiqib ketmasin.

### 5. Video fayl talablari

- Video **H.264 (MP4)** formatda bo'lishi kerak — iOS Safari boshqa kodeklarni o'ynatmasligi mumkin. Filament yuklash maydonida `acceptedFileTypes(['video/mp4'])` qo'yilsin va yordam matnida yozilsin.
- Katta videolar uchun `preload="auto"` + storage'dan `Accept-Ranges` bilan berilishi (Laravel `response()->file()` yoki to'g'ridan-to'g'ri `storage:link` orqali public URL) — streaming ishlashi uchun.

## Qabul mezonlari (acceptance criteria)

1. Admin panelda video + marker rasm yuklanadi, `.mind` fayl avtomatik (yoki qo'lda) tayyorlanadi.
2. QR kod PNG/SVG yuklab olinadi va u to'g'ri `/ar/{uuid}` manzilga olib boradi.
3. Android Chrome va iOS Safari'da: QR skaner → kamera ochiladi → chop etilgan (yoki ekrandagi) marker rasmga qaratilganda video rasm ustida jonlanadi, rasm siljisa video birga siljiydi.
4. Rasmdan uzoqlashtirilsa video pauza bo'ladi, qaytarilsa davom etadi.
5. `ar_enabled=false` yoki `.mind` yo'q bo'lsa — tushunarli xato sahifa.
6. Mavjud RBAC va `VideoResource` funksiyalari buzilmagan.

## Test qilish tartibi

1. `php artisan migrate`, `php artisan storage:link`.
2. Lokal HTTPS uchun: `php artisan serve` + telefonda test qilish uchun **ngrok** yoki `valet share` (kamera HTTPS talab qiladi). READMEga yozib qo'y.
3. Marker rasmni monitor ekranida ochib, telefon bilan test qilish mumkin — chop etish shart emas.

## Ehtiyot bo'lish kerak bo'lgan joylar

- Filament versiyasi (3 yoki 4) — sintaksis farq qiladi, avval tekshir.
- `.mind` kompilyatsiya og'ir jarayon — hech qachon request ichida sinxron qilma, faqat queue.
- Marker rasm sifati past bo'lsa tracking ishlamaydi — bu kod xatosi emas, admin uchun yordam matnida tushuntir.
- CORS: video va `.mind` fayllar shu domen `storage` dan berilsa muammo bo'lmaydi; CDN ishlatilsa `crossorigin` sozla.
