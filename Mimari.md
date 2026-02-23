# Codron Merkezi Sistem – Son Mimari

Bu doküman, **deamon** (çekirdek), **panel** (web.codron.co) ve **CDN** (cdn.web.codron.co) için verilmiş son kararları tek yerde toplar.

---

## 1. Genel yapı

| Bileşen | Konum | Rol |
|--------|------|-----|
| **Deamon** | Composer paketi (`codron/deamon`) | Çekirdek: routing, modüller, DB katmanı, şablon motoru (Twig). Müşteri siteleri bunu kullanır; yeni özellikler (blog, sanal pos vb.) modül olarak eklenir. |
| **Panel** | https://web.codron.co (VPS) | Merkezi yönetim: site içerikleri (blog, ürün, proje, ayarlar), medya yönetimi, Composer token'ları. Süper admin + site admin rolleri. |
| **CDN** | https://cdn.web.codron.co (VPS) | Tüm asset'ler: fotoğraf, video, belge, logo vb. Upload API; panel ve siteler buraya yükler, URL'ler buradan servis edilir. |

- **Müşteri siteleri:** Hostinger paylaşımlı hosting; webhook ile otomatik deploy; Composer ile deamon ve bağımlılıklar yüklenir.
- **Deamon, Panel, CDN:** Senin VPS'inde çalışır.

---

## 2. Deamon (çekirdek)

### Özellikler

- **Composer ile yüklenir:** `composer require codron/deamon` (private paket; token panel üzerinden verilir).
- **Repo:** https://github.com/codron-co/deamon (private).
- **İçerik:** Routing, modül yapısı (`module/`), veritabanı katmanı (MySQL/MariaDB), şablon motoru (**Twig**). Tema/override mantığı çekirdekte tanımlı.
- **Genişletilebilir:** Özellikler modül olarak eklenir. Örn. şu an blog modülü, ileride sanal pos, e-ticaret, form builder vb. Yeni modül = deamon'a eklenen kod + panel tarafında ilgili yönetim ekranları.

### Modül yapısı (İngilizce)

- Tüm kod ve modül isimleri **İngilizce**. Modüller **deamon** içinde `src/Modules/` altında.
- İlk modüller: **Blog**, **Contact**, **About**, **SubscriptionForm**, **Products**, **Services**, **Projects**, **Page** (dinamik sayfalar, panelden oluşturulur).
- Her modül çekirdeğin parçası; site tarafında sadece **override** (tema veya panel üzerinden) ile özelleştirilir.

### Şablon motoru (Twig)

- Twig **deamon içinde** kullanılır; şablonlar çekirdekte tanımlı, site tema/override ile üzerine yazabilir.
- Logic çekirdekte, görünüm Twig şablonları ile ayrılır; güvenlik ve tutarlılık artar.

---

## 3. Panel (web.codron.co)

### Rol

- **Merkezi panel:** Tek adres (web.codron.co); tüm siteler buradan yönetilir. Müşteri kendi sitesine, sen (süper admin) hepsine erişirsin.
- **Site yönetimi:** Blog yazıları, ürünler, projeler, sayfa içerikleri, ayarlar (site adı, logo, iletişim), redirect'ler. Veriler ilgili siteye ait MySQL/MariaDB'de; panel bu DB'lere bağlanır.
- **Medya:** Yükleme işlemi CDN API'ye gider; panel arayüzünden yüklenen dosyaların URL'leri ilgili alanlara (logo, ürün resmi, blog resmi vb.) yazılır.
- **Composer token'ları:** Panelde token üretilir, doğrulanır ve listelenir. Müşteriler kendi token'larını panelde görür; deploy (Hostinger) bu token ile private deamon paketini çeker. Doğrulama panel tarafında; Git token kullanılmaz.

### Kullanıcı rolleri

| Rol | Yetki |
|-----|-------|
| **Süper admin** | Tüm siteler, tüm ayarlar, kullanıcı/token yönetimi, sistem ayarları. |
| **Site admin** | Sadece atandığı site: içerik (blog, ürün, proje), ayarlar, medya; kendi Composer token'ını görür/yeniler. |

### Repo

- https://github.com/codron-co/web-panel

---

## 4. CDN (cdn.web.codron.co)

### Rol

- **Tüm asset'ler burada:** Fotoğraf, video, belge (PDF vb.), logo, favicon. Panel ve (gerekiyorsa) siteler yükleme için CDN API'yi kullanır; dönen URL DB'de veya ayarlarda saklanır.
- **Şimdilik:** Upload/listeleme/silme **API**; ileride panel içinde medya kütüphanesi arayüzü eklenebilir.
- **Repo:** https://github.com/codron-co/web-cdn

### Örnek API

- **POST** upload: `site_id`, `type` (ürün|blog|logo|belge|video|genel), dosya → yanıt: `url` (https://cdn.web.codron.co/...).
- **GET** list (opsiyonel), **DELETE** (opsiyonel); güvenlik token/panel auth ile.

---

## 5. Veritabanı (MySQL / MariaDB, InnoDB)

- **Her site için ayrı DB.** Süper admin, panelden her sitenin DB bilgilerini (host, db adı, kullanıcı, şifre) girer; panel bu bilgilerle site DB'lerine bağlanır. Site eklendikten sonra **migration** ile tablolar oluşturulur (tek tık veya toplu veya `db/migrate?key=XXX`).
- **Site migration:** Panelde **Migrations** sayfasında hangi sitelerde migration yapıldığı görünür; tek site veya "tüm siteler" için çalıştırılır. Ayrıca `GET /db/migrate?key=XXXXX` ile (key panelde açılıp kapatılabilir) tüm site DB'lerinde migration çalıştırılır.
- **Panel DB migration:** Panel'in kendi veritabanı (codron_panel) da migration ile güncellenir. `migrations/panel/*.sql`; Migrations sayfasında "Run panel migrations" veya `GET /db/migrate-panel?key=XXX` ile çalıştırılır. Manuel SQL gerekmez.
- Tablolar (örnek): `schema_migrations`, `site_pages`, `settings`, kategoriler, ürünler, bloglar vb. Site şeması panelde `migrations/site/*.sql`; panel kendi şeması `migrations/panel/*.sql`.
- Karakter seti: **utf8mb4**.

---

## 6. URL yapısı ve routes (kod karmaşası yok)

- **Tüm URL'ler aynı mantıkta:** `domain.com/module/submodule/action` veya `domain.com/module/action/{id}` veya `domain.com/blog/{kategori-slug}/{yazi-slug}`.
- **Route geldiği zaman sadece o sayfanın kodu çalışır:** Tek bir handler dosyası; dağınık if/else veya büyük switch yok.
- **Panel:** `public/index.php` sadece yönlendirme. **routes.json**: path, method, handler, isteğe bağlı css, js. Eşleşince `pages/{handler}.php` include edilir.
- **Web siteleri (deamon):** Aynı mantık. **routes.json**: path (`/blog/{category}/{slug}` gibi), **handler** (örn. `blog/detail`), isteğe bağlı css, js. Eşleşince `pages/{handler}.php` çalışır (önce tema `theme/pages/`, yoksa çekirdek `deamon/pages/`). O route'e ait tüm kod o dosyada; başka yerde dallanma yok.

---

## 7. Site şablonları / override

- **Çekirdek şablonları** deamon'da (Twig); varsayılan görünüm.
- **Override seçenekleri:**
  - **Repo:** Müşteri sitesi repo'sunda tema klasörü; çekirdek "önce tema dizinine bak, yoksa çekirdek şablonu kullan" mantığı. Deploy ile güncellenir.
  - **Panel (ileride):** İsteğe bağlı olarak panel üzerinden belirli şablonların içeriğini override etme (ör. metin blokları, footer metni). Ağır override'lar (tüm HTML) repo/tema ile, hafif metin/parça panel ile yapılabilir.

Özet: Ağır tema/HTML override = repo (site projesi); hafif, metin odaklı override = panel ile genişletilebilir.

---

## 8. Güncelleme ve token akışı

- **Deamon güncellemesi:** Yeni sürüm tag'lenir; müşteri sitelerinde deploy sırasında `composer update codron/deamon` (veya otomatik) çalışır. Token panelde yönetilir; Hostinger'da Composer, panelin verdiği token ile paketi çeker.
- **Panel/CDN:** VPS'te kendi repolarından webhook ile deploy; ayrı konu.

---

## 9. Özet tablo

| Konu | Karar |
|------|--------|
| Çekirdek | **Deamon**, Composer paketi; Twig, module/, routing, DB katmanı; genişletilebilir (blog, sanal pos vb.). |
| Panel | **web.codron.co** merkezi; site yönetimi (blog, ürün, proje, ayarlar), Composer token'ları, medya yönetimi. Süper admin + site admin. |
| CDN | **cdn.web.codron.co**; tüm asset'ler (foto, video, belge, logo); önce API. |
| Hosting | Müşteri siteleri Hostinger; deamon, panel, CDN VPS. |
| Token | Panelde üretilir/doğrulanır; müşteri panelden görür; Git token değil. |
| Şablon override | Ağır: repo/tema. İsteğe bağlı hafif: panel. |

---

## 10. Yerel geliştirme (web-izyem ile deneme)

- **Deamon:** `deamon/` – `composer install`; modüller `src/Modules/`, şablonlar `templates/`, sayfa handler'ları `pages/`.
- **Panel:** `web-panel/` – `composer install`; panel DB'yi oluştur, Migrations → Run panel migrations (veya ilk kurulumda `migrations/panel/001_initial.sql`); `.env` veya ortam değişkenleri ile `PANEL_DB_*` ver; giriş `public/`.
- **CDN:** `web-cdn/` – `public/index.php`; POST `api/upload`, GET `{site_id}/{type}/...` dosya servisi.
- **web-izyem:** `composer install` (path repo ile deamon bağlanır); `.env` ile `DB_*`, `SITE_ID`, `CDN_URL` ver; site DB'yi oluştur, panelden site ekleyip Migrations ile site migration çalıştır; giriş `public/index.php`.

Bu doküman, son kararlara göre tek referans metindir; yeni modül veya özellik eklerken bu yapıya uyum hedeflenir.
