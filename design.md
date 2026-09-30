# DESIGN.md — Panduan UI/UX Sistem Bengkel AHASS

## 1. Design System & Framework
- **CSS Framework:** Wajib menggunakan **Bootstrap 5** (via CDN untuk setup cepat).
- **Icons:** Gunakan **FontAwesome** atau **Bootstrap Icons** untuk memperjelas aksi (misal: ikon '+' untuk tambah, ikon 'trash' untuk hapus).
- **Warna Tema (AHASS Vibe):**
  - Primary: Merah Honda (`#cc0000` atau varian merah standar).
  - Secondary/Accent: Putih dan Abu-abu terang (`#f8f9fa`) untuk *background*.
  - Success: Hijau (`#198754`) untuk indikator selesai/stok aman.
  - Warning/Danger: Kuning/Merah untuk status *Pending*, *Batal*, atau stok kurang.

## 2. Layout & Navigasi (Dashboard)
- **Struktur Halaman:** 
  - Gunakan layout *Sidebar* di sebelah kiri untuk menu utama (Master Jasa, Master Part, Form SA, Work Order).
  - *Navbar* atas untuk informasi *User* (Service Advisor/Admin) dan tombol Logout.
- **Card-Based UI:** Bungkus semua form dan tabel menggunakan komponen `.card` Bootstrap agar terlihat bersih dan memiliki bayangan (shadow) halus.

## 3. Aturan Form & Konversi (Frictionless UX)
- **Label & Input:** Posisi label selalu di atas input. Gunakan *placeholder* yang deskriptif (misal: `Contoh: BA 1234 AB`).
- **Readonly Fields:** Untuk kolom yang digenerate otomatis (seperti ID Form SA, ID Work Order), buat inputnya menjadi `readonly` dengan *background* abu-abu agar pengguna tahu itu tidak perlu diisi manual.
- **Validasi Visual:** 
  - Jika form error (dari respons AJAX 400), beri *class* `.is-invalid` pada input yang bersangkutan dan tampilkan pesan error berwarna merah persis di bawah input tersebut.

## 4. Handling State (AJAX & Interaksi)
- **Loading State Mutlak:** 
  - Setiap kali tombol submit ditekan (memanggil AJAX), tombol **wajib** di-disable dan teksnya berubah menjadi indikator *loading* (misal: `<span class="spinner-border spinner-border-sm"></span> Memproses...`).
  - Ini untuk mencegah klik ganda (double submit) yang bisa merusak stok.
- **Feedback Sukses/Error:** 
  - Jangan gunakan `alert()` bawaan browser yang kaku. 
  - Wajib gunakan library seperti **SweetAlert2** atau **Toastr** (via CDN) untuk memunculkan notifikasi sukses (hijau) atau gagal (merah) di sudut layar setelah aksi AJAX selesai.

## 5. Tabel Data
- Gunakan `.table-responsive`, `.table-hover`, dan `.table-striped` dari Bootstrap.
- Sediakan fitur pencarian (Search bar) di atas tabel, yang secara asinkron memfilter data tanpa harus me-refresh halaman (gunakan jQuery).
- Berikan badge visual untuk status Work Order (misal: `badge bg-warning` untuk Pending, `badge bg-success` untuk Selesai).
