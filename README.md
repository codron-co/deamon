# deamon

Codron **ana çekirdek** paketi. Müşteri siteleri bu paketi Composer ile yükler; routing, modüller, veritabanı katmanı ve Twig şablon motoru burada tanımlıdır. Yeni özellikler (blog, sanal pos, e-ticaret vb.) modül olarak eklenir.

## Kullanım

Private Composer paketi; token [web.codron.co](https://web.codron.co) panelinden alınır.

```bash
composer require codron/deamon
```

## İçerik

- **Routing:** Tek giriş noktası, URL kuralları (/, /hakkimizda, /urun, /blog vb.)
- **Modüller:** `module/` – header, footer, formlar, slider, blog listesi/detay vb.
- **Veritabanı:** MySQL/MariaDB katmanı (InnoDB, utf8mb4)
- **Şablon motoru:** Twig; şablonlar çekirdekte, site tema/override ile üzerine yazabilir
- **Genişletilebilir:** Yeni modüller (blog, sanal pos, form builder vb.) çekirdeğe eklenir; panel tarafında ilgili yönetim ekranları açılır

## Geliştirme

- Yeni özellik = yeni modül veya mevcut modülün genişletilmesi
- Sürüm tag'lendiğinde müşteri siteleri `composer update codron/deamon` ile güncelleyebilir (deploy sırasında)

## Repo

- https://github.com/codron-co/deamon (private)

## Lisans

Özel paket – Codron.
