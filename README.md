# ESPERPAT Project

Struktur proyek ini terbagi menjadi dua bagian utama:
- **backend/**: Berisi aplikasi CodeIgniter 4 (API & Logika Bisnis).
- **frontend/**: Berisi aplikasi Vue.js 3 (Interface Pengguna).

---

## 🚀 Frontend (Vue.js 3 + Vite)

Aplikasi frontend dibangun menggunakan Vue.js 3 dengan desain premium dan responsif.

### Cara Menjalankan Lokal:
1.  Buka terminal di folder `frontend`: `cd frontend`
2.  Install dependencies: `npm install`
3.  Jalankan server dev: `npm run dev`

### ☁️ Deployment ke Vercel:
1.  Push folder `frontend` ke GitHub (atau gunakan Vercel CLI).
2.  Di dashboard Vercel, pastikan **Output Directory** adalah `dist`.
3.  Tambahkan **Environment Variable**:
    *   `VITE_API_BASE_URL`: Isi dengan URL backend production Anda (contoh: `https://api.esperpat.com/api/`).

---

## ⚙️ Backend (CodeIgniter 4)

Backend berfungsi sebagai API server yang menyediakan data untuk frontend.

### Cara Menjalankan Lokal:
1.  Buka direktori `backend`.
2.  Copy `.env` dan sesuaikan database.
3.  Akses melalui Laragon di: `http://localhost/ESPERPAT/backend/public/`
4.  Endpoint API: `http://localhost/ESPERPAT/backend/public/api/`

---

## 🛠️ Fitur Terpasang
- **Vue Router**: Untuk navigasi Single Page Application (SPA).
- **Axios Client**: Dikonfigurasi dengan interseptor JWT (Token disimpan di localStorage).
- **CORS Enabled**: Backend sudah dikonfigurasi untuk menerima request dari domain berbeda.
- **Vercel Config**: Sudah tersedia `vercel.json` agar routing SPA berjalan lancar di Vercel.
