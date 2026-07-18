# SmartCatalog

Sistem manajemen inventaris dan penjualan cerdas berbasis web untuk UAS Pemrograman Web Lanjutan (PWL). Menggunakan arsitektur Service-Repository dengan role-based dashboard untuk monitoring KPI, transaksi, dan merchant performance secara real-time.

---

## Stack / Teknis

| Layer | Teknologi |
|-------|-----------|
| Bahasa | PHP 8.3, JavaScript (ES6+) |
| Framework | Laravel 13.8 |
| Frontend | Blade, Bootstrap 5.3.3, Lucide Icons v0.460.0, Chart.js 4.4.7 |
| Database | MySQL + Laravel Eloquent ORM |
| Build Tool | Vite 8.0.0 |
| Recommendation | Rule-based engine (analisis tingkat stok + velocity penjualan) |
| Keamanan | Account lockout, rate limiting, CSRF, audit logging, soft delete |
| Tipografi | Google Fonts Inter (400–800) |

---

<img width="1437" height="784" alt="Screenshot 2026-07-18 at 09 37 05" src="https://github.com/user-attachments/assets/03fe6181-e84a-4507-9320-fd1214794618" />



## Flow Aplikasi

```
┌─────────┐     ┌──────────┐     ┌──────────────────────────────────┐
│  Login   │────▶│Role Check│────▶│          Dashboard               │
└─────────┘     └──────────┘     │  ┌───────────┐ ┌──────────────┐ │
                                  │  │ Developer │ │ Full KPI +   │ │
                                  │  │           │ │ Charts +     │ │
                                  │  │           │ │ User Mgmt    │ │
                                  │  ├───────────┤ ├──────────────┤ │
                                  │  │  Owner    │ │ KPI +        │ │
                                  │  │           │ │ Merchant +   │ │
                                  │  │           │ │ Reports      │ │
                                  │  ├───────────┤ ├──────────────┤ │
                                  │  │ Pegawai   │ │ Top Products │ │
                                  │  │           │ │ + Low Stock  │ │
                                  │  ├───────────┤ ├──────────────┤ │
                                  │  │   User    │ │ Stats +      │ │
                                  │  │           │ │ Profile      │ │
                                  │  └───────────┘ └──────────────┘ │
                                  └──────────────────────────────────┘
                                            │
            ┌───────────────────────────────┼───────────────────────────────┐
            ▼                               ▼                               ▼
   ┌─────────────────┐           ┌──────────────────┐           ┌──────────────────┐
   │    Products      │           │      Sales        │           │      Stock        │
   │  CRUD Produk     │           │  Pilih Product +  │           │  Stok Masuk /     │
   │  ↓               │           │  Merchant Code    │           │  Stok Keluar      │
   │  Cache Clear     │           │  ↓                │           │  ↓                │
   │  Dashboard       │           │  Validasi Stok    │           │  Update Stock     │
   │  Refresh         │           │  ↓                │           │  ↓                │
   └─────────────────┘           │  Buat Transaksi   │           │  Cache Clear      │
                                  │  ↓                │           │  Dashboard        │
                                  │  Generate Struk   │           │  Refresh          │
                                  └──────────────────┘           └──────────────────┘
                                            │
                                            ▼
                                  ┌──────────────────┐           ┌──────────────────┐
                                  │     Reports       │           │    Merchants      │
                                  │  Filter Data      │           │  Daftar Merchant  │
                                  │  ↓                │           │  ↓                │
                                  │  Export CSV / PDF │           │  Detail Analytics │
                                  └──────────────────┘           │  Revenue Trend    │
                                                                  │  Top Products     │
                                                                  └──────────────────┘
```

### Alur Detail

1. **Authentication** → Login dengan email + password → Cek status akun (active/locked/suspended) → Redirect ke dashboard sesuai role
2. **Products** → CRUD produk (kode, nama, kategori, harga, stok, minimum stok) → Otomatis clear cache dashboard
3. **Sales** → Pilih produk + merchant + qty → Validasi stok mencukupi → Generate nomor transaksi `TRX-YYYYMMDD-XXXX` → Simpan → Update stok produk → Clear cache → Cetak struk
4. **Stock** → Pilih produk + tipe (in/out) + qty → Generate kode stok `STK-YYYYMMDD-XXXX` → Update stok produk → Clear cache
5. **Reports** → Filter berdasarkan tanggal/merchant → Export ke CSV atau PDF
6. **Merchants** → Lihat 5 merchant dengan revenue, transaksi, growth → Detail per-merchant (top products, revenue trend, recent transactions)
7. **Recommendation Engine** → Analisis stok vs minimum → Prediksi hari kehabisan → Rekomendasi restock + promosi

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Developer | `developer@smartcatalog.com` | `Developer#2026` |
| Owner | `owner@smartcatalog.com` | `Owner#2026` |
| Pegawai | `pegawai@smartcatalog.com` | `Pegawai#2026` |
| User | `user@smartcatalog.com` | `User#2026` |

---

## Database Schema

```
┌──────────────┐       ┌──────────────┐       ┌──────────────────┐
│    users      │       │   products    │       │     sales        │
│──────────────│       │──────────────│       │──────────────────│
│ id            │       │ id            │       │ id                │
│ name          │       │ product_code  │       │ transaction_number│
│ username      │       │ product_name  │◀──┐   │ transaction_date  │
│ email         │       │ category      │   │   │ merchant_code     │
│ password      │       │ price         │   │   │ product_id ──────▶│
│ role          │       │ stock         │   │   │ qty               │
│ status        │       │ minimum_stock │   │   │ price             │
│ avatar        │       └──────────────┘   │   │ subtotal          │
│ last_login_at │                          │   │ grand_total       │
│ locked_until  │       ┌──────────────────┘   │ payment_method    │
└──────────────┘       │ stock_transactions   │ payment_status    │
                        │──────────────────│   └──────────────────┘
                        │ id                │
                        │ stock_code        │
                        │ stock_date        │   ┌──────────────┐
                        │ type (in/out)     │   │   merchants   │
                        │ product_id ──────▶│   │──────────────│
                        │ qty               │   │ id            │
                        └──────────────────┘   │ code          │
                                                │ name          │
┌──────────────┐                               │ description   │
│  audit_logs   │                               │ location      │
│──────────────│                               │ icon          │
│ id            │                               │ status        │
│ user_id       │                               └──────────────┘
│ event         │
│ auditable_type│
│ auditable_id  │
│ old_values    │
│ new_values    │
│ ip_address    │
└──────────────┘
```

### Tabel Utama

| Tabel | Records | Keterangan |
|-------|---------|------------|
| `users` | 4 | 4 akun demo (developer, owner, pegawai, user) |
| `products` | 15 | Produk kopi, makanan, minuman |
| `sales` | 150 | Transaksi 90 hari terakhir, random merchant |
| `stock_transactions` | 30 | Stok masuk/keluar |
| `merchants` | 5 | MCH-001 s/d MCH-005 |
| `audit_logs` | - | Log semua operasi CRUD |

---

## Cara Instalasi

```bash
# Clone
git clone <url>
cd uas_pwl

# Install
composer install
npm install

# Setup
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --force

# Build & Run
npm run build
php artisan serve
```

Buka `http://localhost:8000`
