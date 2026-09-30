# AGENTS.md — Sistem Internal Bengkel AHASS Menara Agung

Aplikasi berbasis web Laravel untuk mengelola operasional harian bengkel AHASS mulai dari pendaftaran Service Advisor, work order, manajemen stok suku cadang, hingga pengiriman nota otomatis ke server pusat Main Dealer.

## Product context
- **Problem:** Pengelolaan operasional bengkel AHASS secara manual rentan terhadap kesalahan pencatatan stok suku cadang, keterlambatan pelaporan data ke Main Dealer, dan kesulitan melacak riwayat servis konsumen.
- **Goals:** Mencatat 100% data Form Service Advisor dan Work Order secara digital dengan format ID auto-generate yang akurat.; Mengurangi kesalahan stok suku cadang melalui pemotongan otomatis QtyRFS dan QtyBook saat transaksi.; Mengirimkan data transaksi (Nota Jasa dan Suku Cadang) ke API Main Dealer secara otomatis saat Work Order berstatus Selesai.
- **Non-goals (do NOT build yet):** Sistem pembayaran pihak ketiga atau gateway pembayaran online.; Aplikasi mobile native untuk mekanik.; Portal publik untuk pendaftaran booking online mandiri oleh pelanggan.

## Tech stack (use these)
- Frontend: Blade Template, jQuery, AJAX, Bootstrap
- Backend: Laravel 10
- Database: MySQL
- Hosting: VPS Ubuntu / cPanel
- Notes: Dealer ID dikunci pada nilai 36915 sesuai standar AHASS Menara Agung.

## Architecture
Aplikasi monolithic berbasis Laravel dengan Blade Template untuk antarmuka pengguna dan jQuery AJAX untuk interaksi asinkron yang cepat di lingkungan bengkel.
- **Frontend Blade & jQuery** — Menyediakan antarmuka form interaktif bagi Service Advisor, Mekanik, dan Kasir.
- **Laravel Controller & Services** — Memproses logika bisnis, validasi stok, auto-generate ID, dan komunikasi HTTP client ke API Pusat.
- **MySQL Database** — Menyimpan seluruh data master, transaksi, dan log integrasi secara persisten.

## Data model (implement this schema)
### `master_jasa`
Tabel master data jasa servis

- `id_job` string — PK, NOT NULL — Primary Key
- `kode_motor` string — NOT NULL — Kode tipe motor
- `keterangan` text — NOT NULL — Deskripsi jasa
- `harga` integer — NOT NULL — Harga jasa
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `master_part`
Tabel master suku cadang

- `part_number` string — PK, NOT NULL — Primary Key
- `nama_part` string — NOT NULL — Nama suku cadang
- `harga` integer — NOT NULL — Harga part
- `qty_stock` integer — NOT NULL, default 0 — Stok fisik total
- `qty_rfs` integer — NOT NULL, default 0 — Ready for Sale stock
- `qty_book` integer — NOT NULL, default 0 — Booked stock
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `master_mekanik`
Tabel data mekanik bengkel

- `id` integer — PK, NOT NULL — Primary Key
- `honda_id_mekanik` string — unique, NOT NULL — ID mekanik resmi
- `nama_mekanik` string — NOT NULL — Nama lengkap mekanik
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `master_customer`
Tabel data konsumen

- `id` integer — PK, NOT NULL — Primary Key
- `id_customer` string — unique, NOT NULL — Kode customer unik
- `nama_customer` string — NOT NULL — Nama konsumen
- `no_plat` string — NOT NULL — Nomor plat kendaraan
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `form_service_advisor`
Form pendaftaran oleh Service Advisor

- `id_form_sa` string — PK, NOT NULL — Primary Key format SA/TAHUN/36915/0001
- `id_customer` string — NOT NULL — Relasi customer
- `waktu_form_sa` timestamp — NOT NULL — Waktu pembuatan form
- `keluhan_konsumen` text — NOT NULL — Keluhan kendaraan
- `no_plat` string — NOT NULL — Nomor plat kendaraan
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `work_order`
Tabel Work Order servis

- `id_wo` string — PK, NOT NULL — Primary Key format WO/YY/MM/36915/00001
- `waktu_wo` timestamp — NOT NULL — Waktu WO dibuat
- `status_wo` enum:WorkOrderStatus — NOT NULL, default PENDING — Status pengerjaan
- `id_mekanik` string — NOT NULL — ID mekanik bertugas
- `total_bayar_jasa` integer — NOT NULL, default 0 — Total biaya jasa
- `total_bayar_part` integer — NOT NULL, default 0 — Total biaya part
- `total_bayar_keseluruhan` integer — NOT NULL, default 0 — Total keseluruhan pembayaran
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `detail_work_order`
Detail rincian jasa dan part pada Work Order

- `id_detail` integer — PK, NOT NULL — Primary Key
- `id_wo` string — NOT NULL — Relasi ke work_order
- `jenis_penanganan` string — NOT NULL — Jasa atau Part
- `biaya` integer — NOT NULL — Biaya satuan
- `deskripsi` text — nullable — Keterangan penanganan
- `qty` integer — NOT NULL, default 1 — Kuantitas
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `nota_jasa_bengkel`
Nota Jasa Bengkel (NJB)

- `id_njb` string — PK, NOT NULL — Primary Key format NJB/YY/MM/36915/00001
- `id_wo` string — NOT NULL — Relasi ke work_order
- `waktu_njb` timestamp — NOT NULL — Waktu terbit NJB
- `id_mekanik` string — NOT NULL — ID mekanik
- `tipe_unit` string — NOT NULL — Tipe unit motor
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `nota_suku_cadang_so`
Header Nota Suku Cadang SO

- `no_nsc` string — PK, NOT NULL — Primary Key format NSC/YY/MM/36915/00001
- `tgl_nsc` timestamp — NOT NULL — Tanggal NSC
- `id_customer` string — NOT NULL — Relasi customer
- `amount` integer — NOT NULL, default 0 — Jumlah awal
- `id_wo` string — NOT NULL — Relasi work_order
- `diskon_part` integer — NOT NULL, default 0 — Diskon suku cadang
- `total_amount` integer — NOT NULL, default 0 — Total akhir amount
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

### `nota_suku_cadang_h3`
Detail NotaSukuCadangH3

- `id` integer — PK, NOT NULL — Primary Key
- `no_nsc` string — NOT NULL — Relasi nota_suku_cadang_so
- `part_number` string — NOT NULL — Relasi master_part
- `qty` integer — NOT NULL, default 1 — Kuantitas part
- `harga` integer — NOT NULL — Harga satuan part
- `jumlah_bayar` integer — NOT NULL — Total bayar part
- `id_wo` string — NOT NULL — Relasi work_order
- `created_at` timestamp — NOT NULL — default now()
- `updated_at` timestamp — NOT NULL — auto-update

Relationships:
- detail_work_order.id_wo → work_order.id_wo (many-to-one)
- nota_jasa_bengkel.id_wo → work_order.id_wo (many-to-one)
- nota_suku_cadang_so.id_wo → work_order.id_wo (many-to-one)
- nota_suku_cadang_h3.no_nsc → nota_suku_cadang_so.no_nsc (many-to-one)

## Data uji
1. **Tambah part melebihi QtyRFS**
   - Data: Part Number 082322MBK4LN9, QtyRFS 2, diminta qty 5
   - Harus menghasilkan: Sistem menolak penambahan dengan pesan stok RFS tidak mencukupi.
2. **Selesai Work Order dan kirim nota**
   - Data: Work Order WO/26/07/36915/02606 dengan total jasa 200000 dan part 130000
   - Harus menghasilkan: Nota NJB dan NSC terbit otomatis serta data berhasil dikirim ke API pusat.

## Bukti aplikasinya jalan (smoke test)
- [ ] Login ke aplikasi sebagai Service Advisor → Buat Form Service Advisor baru → Form tersimpan dengan nomor ID format SA.
- [ ] Ubah Form SA menjadi Work Order → Tambahkan Jasa dan Part → Pastikan QtyRFS berkurang dan QtyBook bertambah.
- [ ] Ubah status Work Order menjadi Selesai → Nota NJB dan NSC terbit otomatis → Data terkirim ke API Main Dealer.

## Folder Structure
- `app/Http/Controllers — Controller aplikasi Laravel`
- `app/Services — Layanan integrasi API pusat`
- `resources/views — Blade templates antarmuka pengguna`
- `routes/web.php — Definisi rute web`

## Key Dependencies
- **laravel/framework** (backend) — Core framework web aplikasi
- **guzzlehttp/guzzle** (backend) — HTTP client untuk integrasi API pusat

> Pin the latest stable version at install time — the names are the decision, not the numbers.

## Environment Variables
- `DB_DATABASE` (backend, required) — Nama database MySQL
- `MAIN_DEALER_API_URL` (backend, required) — URL endpoint server pusat Main Dealer

## API Endpoints
- `POST /api/work-orders/:id/complete` — Menyelesaikan Work Order, menerbitkan nota NJB & NSC, dan mengirim payload ke API Main Dealer. _(auth, fitur: Penyelesaian dan Integrasi API Pusat)_
  - Isi `data`: kolom `work_order` (id_wo, waktu_wo, status_wo, id_mekanik, total_bayar_jasa, total_bayar_part, total_bayar_keseluruhan)
  - Kode error khas: `API_CONNECTION_FAILED`, `INSUFFICIENT_STOCK`

## Roles (RBAC)
- **ADMIN** — Administrator sistem bengkel · kelola master data, melihat laporan, manajemen user · semua baris
- **SERVICE_ADVISOR** — Petugas pendaftaran servis · buat form sa, buat work order · semua baris

## Layanan pihak ketiga
- **Main Dealer API** (other) — dipakai oleh: Penyelesaian dan Integrasi API Pusat · env: `MAIN_DEALER_API_URL`

## Target kapasitas & kecepatan
- Pengguna bersamaan di jam sibuk: **15**
- `work_order` — 100 baris saat rilis, +1.000/bulan → **≈ 12.100 baris dalam 12 bulan** (tabel gemuk: pastikan ada index)
- `detail_work_order` — 300 baris saat rilis, +3.000/bulan → **≈ 36.300 baris dalam 12 bulan** (tabel gemuk: pastikan ada index)
- Penyimpanan Work Order dan kalkulasi stok: **p95 < 1000 ms** pada 5.000 baris

## Enums
- **WorkOrderStatus**: PENDING, PROSES, SELESAI, CANCEL _(work_order.status_wo)_

## Working rules
- Ship the MVP scope only. Anything under "Non-goals" is out of scope.
- Every feature must meet its acceptance criteria before it is considered done.
- Keep changes small and reviewable; follow existing patterns in the repo.

## Backend conventions (follow exactly)
- **API response envelope** — every JSON response uses `{ "success": boolean, "message": string, "data"?: any }`. Errors: `{ "success": false, "message": string }`. Never return bare arrays/objects.
- **Central error handling** — one global error handler at the framework boundary; map validation, ORM (e.g. Eloquent/Doctrine), and auth errors to the right HTTP status. No try/catch scattered per-route returning ad-hoc shapes.
- **Input validation** — validate the body/params of every POST/PUT/PATCH at the boundary (Form Requests in Laravel, or the framework's validator) before touching the DB.
- **List endpoint contract** — every list/index endpoint accepts `?page=&limit=&search=&sortBy=&sortOrder=` and returns paginated data (never dump the whole table).
- **Security baseline** — set security headers, CORS (allow-list origins), rate limiting, and gzip/compression — in the app or at the reverse proxy.
- **Structured logging** — structured logging via Monolog; no bare `echo`/`var_dump` in production paths.

## API contract (follow exactly)
- **Status code sesuai situasi** — 200 baca/ubah berhasil; 201 + header `Location` untuk resource baru; 204 hapus berhasil; 400 input tidak valid; 401 belum login; 403 sudah login tapi tidak berhak; 404 tidak ada (ATAU bukan milikmu); 409 bentrok aturan bisnis; 413 payload kelewat besar; 429 kena rate limit + header `Retry-After`; 500 hanya untuk bug yang tak terduga.
- **`success:false` SELALU berpasangan dengan status ≥ 400** — TIDAK ADA error ber-status 200. Klien yang memeriksa `res.ok` akan diam saat gagal dan menampilkan kegagalan sebagai keberhasilan; ini kelas bug paling senyap yang ada.
- **Envelope error** — `{ success: false, message, code, errors? }`. `code` SCREAMING_SNAKE (`VALIDATION_ERROR`, `NOT_FOUND`, `REF_IN_USE`, `DUPLICATE`, `INSUFFICIENT_STOCK`) supaya klien bercabang pada kode, bukan pada kalimat. `errors` hanya untuk 400: `{ nama_field: "pesan" }` — inilah yang dipakai UI untuk menempelkan pesan di bawah field yang gagal.
- **Bentuk respons list** — `data: { items, total, page, limit, totalPages }`. `total` = jumlah baris SETELAH filter dan SEBELUM dipotong halaman; salah menafsirkan ini membuat komponen paginasi menampilkan jumlah halaman yang keliru.
- **Kontrak list** — `limit` default 20, MAKSIMAL 100; nilai lebih besar DIPOTONG ke 100, bukan ditolak. `sortBy` hanya boleh dari allow-list kolom di kode — jangan pernah meneruskan nilainya mentah ke `ORDER BY`.
- **Rentang tanggal baku** — selalu `?from=YYYY-MM-DD&to=YYYY-MM-DD`, inklusif di kedua ujung, ditafsirkan sebagai tanggal Asia/Jakarta. Satu nama parameter untuk seluruh aplikasi; kalau tiap endpoint menamainya sendiri, filter tanggal gagal diam-diam.
- **Batas ukuran** — body JSON maksimal 1 MB, ditolak 413 SEBELUM di-parse; array dalam satu request maksimal 200 item; endpoint ekspor tanpa paginasi maksimal 5.000 baris, di atas itu jadi pekerjaan latar.
- **Penamaan payload = nama kolom** — field JSON memakai `snake_case` yang PERSIS sama dengan nama kolom database. Beda gaya antara backend dan frontend (`customerId` vs `customer_id`) menghasilkan 400 yang baru ketahuan saat integrasi.
- **Jangan pernah kirim kolom rahasia** — kolom password/hash/token/secret/OTP/api-key DILARANG muncul di respons mana pun. Pilih kolom secara eksplisit, jangan mengandalkan penghapusan setelah query.

## Code structure (follow exactly)
- **Route/controller tipis** — hanya membaca request, memvalidasi (Form Requests in Laravel, or the framework's validator), memanggil service, dan membentuk respons. Tanpa query, tanpa aturan bisnis.
- **Service adalah SATU-SATUNYA tempat aturan bisnis** — dan wajib bisa dipanggil dari route, dari pekerjaan terjadwal, maupun dari skrip CLI TANPA perubahan. Kalau sebuah aturan hanya bisa dijalankan lewat HTTP, ia salah tempat.
- **Hanya lapisan data yang menyentuh ORM (e.g. Eloquent/Doctrine)/SQL** — service memanggil repository, bukan ORM langsung.
- **Arah impor satu arah** — route → service → repository. Repository tidak boleh mengimpor service; service tidak boleh mengimpor route.
- **Rumus yang dipakai di lebih dari satu tempat WAJIB satu fungsi bersama** — perhitungan harga di layar kasir dan di laporan yang ditulis dua kali akan menyimpang perlahan, dan selisihnya baru ketahuan saat rekonsiliasi.

## Data conventions (follow exactly)
- **Uang tidak pernah float** — simpan sebagai `decimal(12,2)` atau sebagai integer dalam satuan terkecil (rupiah bulat). Float membuat penjumlahan meleset beberapa sen; laporan tidak akan pernah balance dan penyebabnya tidak akan pernah terlihat di layar.
- **Waktu disimpan UTC (`timestamptz`), ditampilkan WIB** — konversi ke Asia/Jakarta HANYA saat menampilkan dan saat mengelompokkan per hari/bulan. Laporan "hari ini" yang dihitung memakai waktu server bergeser 7 jam untuk pengguna Indonesia. Hari = 00:00–23:59 WIB, minggu mulai Senin.
- **Nomor HP dinormalkan saat MENULIS** — simpan satu bentuk saja (`+62…`). Campuran `08xx` dan `+62xx` menghasilkan pelanggan ganda dan pesan WhatsApp yang tidak terkirim.
- **Hapus induk = RESTRICT** — semua foreign key memakai `ON DELETE RESTRICT ON UPDATE CASCADE`. JANGAN pernah memilih CASCADE sendiri: menghapus satu pelanggan tidak boleh ikut menghapus riwayat transaksinya. Penolakan dijawab 409 dengan `code: "REF_IN_USE"` dan pesan yang MENYEBUTKAN siapa yang masih memakainya.
- **Satu strategi ID** — pakai UUID untuk seluruh proyek; jangan campur serial di satu tabel dan uuid di tabel lain. Entitas yang muncul di URL publik memakai `slug`/`code` terpisah, bukan ID mentahnya.
- **Kolom status wajib punya default** — tanpa itu baris baru bernilai NULL dan setiap dashboard yang memfilter status akan melewatkannya tanpa error.
- **Validasi dua lapis** — setiap acceptance criteria yang berbunyi "ditolak bila …" ditegakkan di DATABASE (constraint/unique/check) DAN di aplikasi. Pelanggaran constraint dipetakan ke 409, jangan dibiarkan bocor sebagai 500.
- **Index** — buat index untuk setiap foreign key, untuk kombinasi kolom yang difilter+diurut bersama di layar utama, dan index parsial `WHERE deleted_at IS NULL` untuk tabel soft-delete. Tanpa ini query dashboard memindai seluruh tabel dan baru terasa setelah data menumpuk.
- **Jangan N+1, jangan `SELECT *` pada tabel gemuk** — hitung agregasi DI DATABASE, jangan tarik semua baris ke aplikasi lalu dijumlahkan di sana.
- **Migrasi** — pakai migrasi bawaan framework (Laravel Migrations / Doctrine Migrations). Maju terus: JANGAN pernah menyunting migrasi yang sudah pernah jalan di produksi; buat migrasi baru. Perubahan yang merusak dilakukan TIGA LANGKAH: (1) tambah kolom nullable, (2) isi data lama, (3) baru jadikan NOT NULL.
- **Seed dipisah** — seed master (data yang tanpa itu aplikasi tidak bisa jalan) selalu aman dijalankan ulang; seed demo hanya berjalan bila `SEED_DEMO=true` dan TIDAK PERNAH di produksi.
- **Data pribadi** — kolom berikut JANGAN PERNAH ditulis ke log, ke pesan error, atau ke URL: `master_mekanik.honda_id_mekanik`, `master_mekanik.nama_mekanik`, `work_order.id_mekanik`, `nota_jasa_bengkel.id_mekanik`. Hapus akun = ANONIMISASI baris (kosongkan kolom-kolom itu), bukan `DELETE FROM users` — dan bukan pula soft delete, yang justru menyimpan data pribadi selamanya.
- **Backup** — kalau database-nya terkelola, nyalakan point-in-time recovery dan catat retensinya di README; kalau self-host, dump harian ke penyimpanan DI LUAR server itu. Keduanya wajib ditutup dengan satu kali uji pulih ke database kosong sebelum rilis — backup yang tak pernah dipulihkan itu cuma rumor.

## Stack discipline (follow exactly)
- **Kunci versi mayor saat install** — pasang versi stabil terbaru, lalu CATAT versi mayor yang benar-benar terpasang di README dan ikuti dokumentasi versi ITU. Menulis kode gaya v3 ke atas paket v4 (atau sebaliknya) membuat build gagal di jam pertama, dan pesan errornya tidak menyebut versi.
- **Stack dibekukan** — JANGAN mengganti framework, ORM, atau database di tengah pengerjaan. Kalau pilihan yang ada benar-benar menghambat, BERHENTI dan laporkan ke pemilik; jangan memigrasi separuh kode lalu meninggalkan dua cara melakukan hal yang sama.
- **Satu package manager, satu lockfile, di-commit** — jangan mencampur npm dan pnpm/yarn di satu repo.
- **Jangan menambah dependency di luar daftar** tanpa alasan yang ditulis. Setiap paket baru adalah hal yang harus dirawat pemilik selamanya.

## Rate limit (follow exactly)
Angka konkret, ditegakkan DI APLIKASI (bukan "di app atau di reverse proxy" — kalimat itu persis celah yang dipakai untuk melewatkannya):
- **Baca**: 120 request/menit per IP.
- **Tulis**: 20 request/menit per pengguna.
- **Login & reset password**: 5 percobaan GAGAL per 15 menit, dihitung per akun DAN per IP, dengan jeda yang membesar. JANGAN mengunci akun permanen — itu justru memberi penyerang cara mengunci akun korban.
- **Kirim pesan/email**: 30 per jam per pengguna. Endpoint kirim yang tidak dibatasi bisa dipakai orang lain sampai kuota providermu habis — itu tagihan nyata ke pemilik.
- **Ekspor**: 10 per jam per pengguna.
- Yang melewati batas dijawab **429 + header `Retry-After`**, bukan didiamkan.

## Frontend/UX conventions (follow exactly)
- **Design tokens once** — define ONE accent color (neutral base like zinc/slate + a single confident accent; no purple/neon glow), ONE type family, ONE radius scale, and a spacing scale on a 4px grid. Reuse tokens everywhere; never hardcode ad-hoc colors/spacing per component.
- **Typography hierarchy** — establish scale by weight + size, not raw hugeness; body text max ~65ch line length and readable leading; use tabular-nums for aligned numeric data.
- **Async states** — every data view handles loading (skeletons that match the final layout, not a bare spinner), empty (composed + shows how to populate), and error (inline for forms, toast for transient). Confirm dialog before destructive/irreversible actions.
- **Forms** — label ABOVE input, error text BELOW; never placeholder-as-label. Show errors next to the field that failed. Never block paste in inputs.
- **Accessibility (WCAG AA)** — text contrast ≥ 4.5:1; visible focus ring on every interactive element; aria-label on icon-only buttons; full keyboard navigation; honor prefers-reduced-motion.
- **Reusable primitives** — build atomic components (Button, Input, Select, Modal/Dialog, Table, Pagination, Badge, Card, Toast) once and compose; do not repeat markup.
- **Motion restraint** — animate only transform/opacity, interaction feedback ≤ 200ms, ease-out on entrance; no infinite loops or decorative motion. Never animate layout properties (width/height/top/left).
- **Anti-generic** — no pure #000000 (use off-black), no three identical equal-width feature cards as the default layout, no em-dash as a decorative separator, no lorem ipsum or placeholder names in shipped screens (use realistic domain content).
- **Responsive** — mobile-first; every multi-column layout collapses cleanly to one column below 768px with no horizontal scroll. Use CSS grid over flex percentage math. Full-height sections use min-h-[100dvh], never h-screen.
- **Routing** — provide a not-found (404) page and sensible loading boundaries.

## Auth policy — Email + password (follow exactly)
- **Password di-hash dengan bcrypt/argon2**, tidak pernah disimpan atau di-log dalam bentuk apa pun. Minimal 8 karakter, dan JANGAN memaksa aturan karakter aneh yang membuat orang menulisnya di kertas.
- **Jangan bocorkan keberadaan akun** — "email atau password salah" untuk kedua kasus, dengan waktu respons yang sama.
- **Reset password MANUAL** — tidak ada alur lupa-password di aplikasi; ini sudah dicatat di Non-Goals dan pemilik meresetnya lewat database. Jangan diam-diam membangun alurnya.
- **Otorisasi tingkat BARIS, bukan hanya peran** — setiap route yang menerima `:id` WAJIB memastikan baris itu milik pengguna sesi SEBELUM membaca atau menulis. Memeriksa peran BUKAN otorisasi. Balas **404** (bukan 403) untuk baris milik orang lain, supaya id-nya tidak bisa ditebak satu per satu.
- **Default tertutup** — endpoint yang tidak secara eksplisit ditandai publik WAJIB butuh login. Kelalaian harus jatuh ke arah terkunci, bukan terbuka.
- **Sesi di browser** — cookie `HttpOnly` + `Secure` + `SameSite=Lax`. Karena sesinya cookie, setiap request yang MENGUBAH data wajib membawa token CSRF. Tanpa itu situs lain bisa memicu aksi atas nama pengguna yang sedang login.
- **Proteksi rute di frontend** — menyembunyikan tombol BUKAN otorisasi; server tetap memeriksa. Sesi habis mengarahkan ke `/login?next=<halaman-asal>` supaya pengguna kembali ke tempatnya, bukan ke layar kosong.
- **Batas percobaan login** — lihat blok Rate limit. Jangan mengunci akun permanen; jeda yang membesar sudah cukup dan tidak bisa dipakai untuk mengunci akun orang lain.

## External-service conventions (follow exactly)
- **Timeout eksplisit** — setiap panggilan keluar diberi batas 10 detik. Panggilan tanpa timeout menggantung sampai seluruh request pengguna ikut mati.
- **Retry hanya untuk kegagalan sementara** — maksimal 3 kali dengan jeda membesar, HANYA untuk timeout/gangguan jaringan/5xx. JANGAN PERNAH mengulang 4xx: itu permintaan yang salah, mengulanginya cuma mengulang kesalahan yang sama.
- **Respons pihak ketiga adalah input TIDAK TEPERCAYA** — validasi bentuknya sebelum dipakai; provider mengubah formatnya tanpa memberitahumu.
- **Jangan gagal diam-diam** — kegagalan final dicatat di baris transaksinya DAN diberitahukan ke pemilik. Kegagalan yang hanya masuk log adalah kegagalan yang tidak pernah diketahui.
- **Kunci anggaran** — JANGAN menambahkan layanan berbayar apa pun yang tidak terdaftar di daftar integrasi PRD ini. Bila sebuah fitur benar-benar membutuhkannya, HENTIKAN dan tulis di Open Questions; jangan mendaftarkan pemilik ke tagihan baru atas inisiatif sendiri.

## Performance (follow exactly)
- **Ukuran connection pool** — sekitar 10 (perkiraan 15 pengguna bersamaan di jam sibuk). Di lingkungan serverless setiap invocation membuka koneksinya sendiri — WAJIB lewat connection pooler, dan pool besar justru menghabiskan kuota koneksi database.
- **Endpoint mahal dibatasi 1 permintaan bersamaan per pengguna** — permintaan kedua ditolak 429 dengan pesan "proses sebelumnya masih berjalan", bukan diantrekan diam-diam sampai server penuh.
- **Aturan cache yang penting bukan cara membuatnya, tapi cara MEMBUANGNYA** — kunci cache memuat SELURUH parameter filter (dan id pengguna bila datanya per-pengguna), dan setiap penulisan ke tabel sumber menghapus kunci yang bertanda tabel itu. Kalau ragu: lebih baik lambat daripada salah.
- **Pekerjaan yang bisa melewati 5 detik TIDAK boleh berada di dalam request** — POST membalas 202 + `jobId`, klien memantau endpoint status, dan job-nya menyimpan checkpoint agar bisa dilanjutkan. Ekspor/impor yang dijalankan sinkron akan kena timeout gateway dan meninggalkan data setengah jadi — itu lebih buruk daripada sekadar lambat.

## Operations (follow exactly)
- **Sediakan `GET /health`** — mengembalikan 200 hanya bila proses hidup DAN database bisa dihubungi. Docker `HEALTHCHECK` dan systemd `restart on failure` bersandar pada sinyal ini; tanpa endpoint-nya, aplikasi yang rusak-tapi-belum-mati tidak pernah dinyalakan ulang.
- **CORS allow-list = nilai `APP_URL`** — bukan `*`, bukan hanya localhost. Satu variabel itu adalah daftar origin yang sah untuk aplikasi ini.
- **Error tracker sejak hari pertama** — dipasang dari sebuah env var (mis. `SENTRY_DSN`), bukan ditunda ke "nanti sebelum rilis". Kalau variabelnya kosong, aplikasi tetap jalan tanpa mengirim apa pun.
- **Request id di setiap baris log** — dan kembalikan id itu di pesan error yang dilihat pengguna. Dengan begitu satu screenshot keluhan sudah cukup untuk menemukan jejaknya; tanpa itu setiap laporan bug dimulai dari nol. Gunakan structured logging via Monolog; no bare `echo`/`var_dump` in production paths.
- **Redaksi wajib di logger** — password, token, OTP, nomor kartu, dan nomor telepon lengkap TIDAK PERNAH masuk log, termasuk saat mencatat body request yang gagal.
- **Pekerjaan latar yang gagal WAJIB memberi tahu pemilik** — ke satu kanal yang sampai ke HP-nya. Cron yang mati diam-diam adalah gangguan yang paling sering tidak terdeteksi: tidak ada error di layar siapa pun, hanya sesuatu yang berhenti terjadi.
- **Hanya DUA lingkungan: lokal dan produksi.** Tidak ada staging. Kunci sandbox dipakai di lokal, kunci produksi hanya ada di server produksi, dan tidak pernah tertukar.
- **Urutan deploy** — build → JALANKAN MIGRASI → baru restart aplikasi → smoke test. Menjalankan migrasi setelah restart membuat aplikasi versi baru menabrak skema lama; ini kesalahan deploy yang paling sering terjadi.

## QA conventions (follow exactly)
- **Definition of done** — sebuah task TIDAK selesai hanya karena happy path terlihat jalan. Selesai = kriteria penerimaannya terpenuhi DAN kamu sudah menjalankan satu perintah test yang bisa dijalankan sekali jalan (mis. `npm test` / `pytest`) dan melihatnya hijau. Kalau kamu tidak bisa memverifikasi, katakan begitu — jangan lapor sukses.
- **Pasang runner lebih dulu** — test runner dipasang di task paling awal, bukan di akhir. Test yang ditunda sampai fitur selesai tidak pernah ditulis.
- **Setiap perbaikan bug menambah satu test** — test yang gagal SEBELUM perbaikan dan lulus SESUDAHNYA. Tanpa ini bug yang sama kembali di rilis berikutnya.
- **Baca `TEST-PLAN.md`** — di sana ada langkah UAT pemilik, kasus negatif, skenario integrasi, dan checklist regresi yang sudah diturunkan dari PRD ini. Jangan mengarang rencana uji sendiri.
- **Wajib punya test otomatis di proyek ini** (diturunkan dari PRD — semuanya perhitungan/agregasi yang salahnya tidak terlihat di layar):
  - rumus kolom `form_service_advisor.id_form_sa` — Primary Key format SA/TAHUN/36915/0001
  - rumus kolom `work_order.id_wo` — Primary Key format WO/YY/MM/36915/00001
  - rumus kolom `nota_jasa_bengkel.id_njb` — Primary Key format NJB/YY/MM/36915/00001
  - rumus kolom `nota_suku_cadang_so.no_nsc` — Primary Key format NSC/YY/MM/36915/00001
  - rumus kolom `nota_suku_cadang_so.diskon_part` — Diskon suku cadang
  - Form Service Advisor & Work Order: Sistem menghasilkan ID Form SA dengan format SA/TAHUN/36915/0001.
- **Jangan uji** tata letak, warna, jarak, atau bunyi teks — itu berubah terus dan test-nya cuma jadi beban. Uji PERILAKU dan ANGKA.
- **Jangan uji** library pihak ketiga. Uji kode kita yang memanggilnya, dengan library itu di-mock.

## Deliverables (must produce)
- README.md — setup, migration, seed, run, build, deploy steps
- Database seed script (master) — an admin user + the reference rows the app cannot start without
- Test seed script (`seed:test`) — loads exactly the rows in TEST-PLAN.md part A into a SEPARATE test database, plus a reset command; never runs against production
- .env.example — every variable with a safe placeholder

## Features & acceptance criteria
### Manajemen Master Data (must-have)
Pengelolaan data Master Jasa, Master Part, Master Mekanik, dan Master Customer.
- [ ] Admin dapat menambah, mengubah, dan menghapus data Master Jasa dan Part.
- [ ] QtyStock, QtyRFS, dan QtyBook pada Master Part tervalidasi angkanya tidak boleh negatif.
- [ ] Pencarian data part dan jasa dapat dilakukan secara cepat via AJAX.

Edge cases to handle:
- [ ] Input harga bernilai negatif ditolak oleh sistem.
- [ ] Penghapusan part yang sedang digunakan dalam Work Order aktif ditolak.

### Form Service Advisor & Work Order (must-have)
Pendaftaran konsumen oleh SA dan pembuatan Work Order dengan ID auto-generate sesuai format dealer 36915.
- [ ] Sistem menghasilkan ID Form SA dengan format SA/TAHUN/36915/0001.
- [ ] Sistem menghasilkan ID Work Order dengan format WO/YY/MM/36915/00001.
- [ ] SA dapat memasukkan keluhan konsumen dan memilih data customer serta mekanik.

Edge cases to handle:
- [ ] Pembuatan WO tanpa memilih Mekanik atau Customer yang valid akan ditolak.
- [ ] Submit ganda dicek dan dicegah dengan disable tombol saat proses AJAX berlangsung.

### Eksekusi Servis & Penanganan Suku Cadang (must-have)
Penambahan item jasa dan part pada Work Order yang memengaruhi kuantitas stok (QtyRFS dan QtyBook).
- [ ] Saat part ditambahkan ke WO, QtyRFS di Master Part dikurangi dan ditambahkan ke QtyBook.
- [ ] Total biaya jasa dan part terhitung otomatis pada rincian Work Order.
- [ ] Detail penanganan mencatat jenis penanganan (Jasa atau Part) secara akurat.

Edge cases to handle:
- [ ] Penambahan part melebihi QtyRFS yang tersedia ditolak oleh sistem dengan pesan stok tidak cukup.
- [ ] Input kuantitas 0 atau negatif pada part ditolak.
- [ ] Penghapusan part dari keranjang Work Order yang berstatus PROSES: QtyBook wajib dikurangi dan dikembalikan seutuhnya ke QtyRFS.

### Penyelesaian dan Integrasi API Pusat (must-have)
Penyelesaian Work Order, penerbitan Nota Jasa Bengkel (NJB), Nota Suku Cadang (NSC), dan pengiriman data ke server Main Dealer.
- [ ] Saat status Work Order diubah menjadi Selesai, kuantitas qty_book dikurangi, dan kuantitas qty_stock (stok fisik total) juga ikut dikurangi secara permanen.
- [ ] Sistem otomatis men-generate ID Nota Jasa (NJB/YY/MM/36915/00001) dan Nota Suku Cadang (NSC/YY/MM/36915/00001).
- [ ] Sistem melakukan autentikasi ke API Main Dealer untuk mendapatkan DGI-API-Key, lalu mengirim payload transaksi secara otomatis.
- [ ] Payload JSON ke API Main Dealer wajib mematuhi struktur keys berikut: noWorkOrder, idCustomer, hondaIdMekanik, noNJB, tanggalNJB, totalHargaNJB, noNSC, tanggalNSC, totalHargaNSC, dealerId, createdTime, modifiedTime, array njb (idJob, hargaServis, createdTime, modifiedTime), dan array nsc (idJob, partsNumber, kuantitas, hargaParts, totalHargaParts, createdTime, modifiedTime).

Edge cases to handle:
- [ ] Gagal koneksi ke server Main Dealer saat pengiriman nota akan mencatat log error dan menyediakan tombol kirim ulang manual.
- [ ] Work Order yang sudah berstatus Selesai tidak dapat diubah kembali detailnya.


## Execution order
- **Total usaha:** ≈ 62 jam-agent untuk satu AI coding agent. Ini bobot untuk mengurutkan pekerjaan, **bukan tanggal dan bukan jam manusia**.
- **Jalur kritis:** T1 → T2 → T4 → T5 → T7 → T8 → T9 ≈ 58 jam-agent. Ini rantai terpanjang yang tidak bisa dikerjakan paralel — kalau ada yang tersendat di sini, seluruh rencana ikut mundur.
- **Bisa dikerjakan sekarang:** `T1` Setup Project Laravel dan Database Schema (membuka 8 task)

- **Pondasi dan Master Data** — 14 jam-agent
  - Selesai bila: Admin dapat melakukan CRUD Master Jasa, Part, Mekanik, dan Customer dengan benar.
- **Operasional Service Advisor & Work Order** — 24 jam-agent
  - Selesai bila: Service Advisor dapat membuat Form SA dan Work Order dengan auto-generate ID yang sesuai.
- **Penyelesaian & Integrasi API Pusat** — 24 jam-agent
  - Selesai bila: Work Order selesai menghasilkan Nota NJB & NSC serta berhasil mengirim data ke API Main Dealer.

## Task backlog (in order)
- **T1** Setup Project Laravel dan Database Schema 🧪 testing — M
- **T2** Implementasi CRUD Master Data — M (after T1)
- **T3** Unit Test Rumus Stok dan Kalkulasi 🧪 testing — S (after T2)
- **T4** Implementasi Form Service Advisor & Auto-Generate ID — M (after T2)
- **T5** Implementasi Work Order & Detail WO — L (after T4)
- **T6** Handling Edge Case Stok Minus dan Submit Ganda ⚠ edge case — S (after T5)
- **T7** Penerbitan Nota NJB dan NSC — M (after T5)
- **T8** Integrasi API Main Dealer (Credentials & Submit) — L (after T7)
- **T9** Handling Edge Case Gagal Koneksi API Pusat ⚠ edge case — S (after T8)

## Ketetapan Kredensial API (Wajib)
- Kredensial untuk DGI-API-Key dibikin statis di dalam `Services` (atau `.env`) menggunakan email `peserta9@example.com` dan password `Y4cH8f`.
