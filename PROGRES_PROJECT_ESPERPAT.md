# PROGRES PROJECT ESPERPAT (Per 21 Februari 2026)

## Yang Sudah Diselesaikan:
1.  **Frontend (Vue.js):**
    *   Desain antarmuka Premium bergaya "Drops Inspired" selesai (Mobile First).
    *   Gaya produk membulat (Rounded Style) dan pewarnaan Black/White + Gradient.
    *   Tampilan Home Page, Products, Kategori, dan Detail Produk (dengan Pull-to-Home refresh gesture).
    *   **Fitur CRUD Produk:** Halaman Admin Manage Products selesai.
    *   **Fitur Manajemen Stok & Harga:** Terdapat modal canggih V3 untuk Update Harga & Restock dengan perhitungan Profit Margin.
    *   **Fitur Upload & Crop Gambar:** Telah ditambahkan Cropper JS (v1.6.1) yang memaksa pengguna men-crop gambar dengan rasio kotak 1:1, diikuti dengan kompresi otomatis (Maks < 20KB).

2.  **Backend (CodeIgniter 4 API):**
    *   API Autentikasi JWT (Login) beserta Session Management berjalan lancar.
    *   Tenggat Expired JWT telah diubah dari 1 jam menjadi **30 Hari** demi kenyamanan mode Development.
    *   Database `esperpat` MySQL telah terkoneksi.
    *   API Manajemen Produk mendukung penerimaan Data Form-Multipart untuk Upload Foto Produk.

3.  **Data Dummy / Seeding:**
    *   Nama-nama produk, Deskripsi, Harga, dan Stok sudah terisi produk sparepart motor yang nyata & rapi (Ada 16 produk).
    *   Seluruh 16 produk telah diunduhkan **Gambar Resolusi Tinggi (HQ)** asli dari internet, di-crop proporsional, dan datanya telah tersimpan di dalam folder lokal `backend/public/uploads/products`.

## Masalah Yang Telah Diatasi (Bugs Fixed):
*   Bug `Failed to parse JSON string. Error: Syntax error` saat Admin mencoba proses Update Produk dengan Foto via FormData -> *FIXED (Diselesaikan di Base Controller melalui deteksi header Multipart).*
*   Bug `Unauthorized 401` saat menaruh Stok karena Sesi Logout -> *FIXED (di-.env).*
*   Bug tampilan `Cropper` layar merah (Error build Vite) -> *FIXED (Downgrade ke versi stabil).*
*   Masalah UI Z-Index jendela Crop tersembunyi di belakang transparansi -> *FIXED.*
*   Membetulkan Target API URL di Vue kembali menuju Localhost (`http://localhost/ESPERPAT/backend/public/api/`).

---

## NEXT STEP (TUGAS SELANJUTNYA):
Pembangunan dan pengerjaan berfokus pada sinkronisasi proses Keranjang ke Pesanan dengan Pembayaran QRIS Manual.

1.  **Membangun UI/UX Checkout QRIS (Sisi Pembeli)**
    *   Modifikasi `CartView.vue` agar menyediakan opsi Pemilihan Metode Pembayaran (Transfer Bank vs QRIS).
    *   Jika QRIS dipilih, tampilkan Barcode QRIS Toko (*Dummy* atau Asli).
    *   Wajibkan pembeli meng-upload gambar *Screenshot / Struk Bukti Transfer* sebelum tombol Pesan sekarang/Checkout bisa dieksekusi.
2.  **Modifikasi Backend `OrderController`**
    *   Menambahkan fungsionalitas Backend untuk menerima file upload dari form pesanan pembayaran.
    *   Menyimpan file bukti tersebut di folder server (misal: `uploads/payments/`).
    *   Mencatat path / nama file bukti tersebut di dalam Tabel Data Base `orders` (Kolom: `bukti_pembayaran` - mungkin perlu _Migration/Alter Table_).
3.  **Membangun Dashboard Verifikasi Pembayaran (Sisi Admin)**
    *   Modifikasi layar list Orders admin agar menampilkan badge tanda bukti bayar telah dilampirkan.
    *   Menambahkan Modal / Pop-up yang membolehkan Admin melihat/memperbesar struk QRIS tersebut untuk divalidasi dengan cek mutasi bank manual.
    *   Persetujuan Pesanan (Ubah status *Pending* -> *Paid / Processed*).
4.  **Deployment (Tahap Akhir)**
    *   Bila keseluruhan program selesai, persiapkan dan hosting backend CI4 ke panel online (cPanel / VPS) beserta Database MySQL nya.
    *   Update `VITE_API_BASE_URL` Vercel ke domain API asli agar Frontend online bisa berfungsi.
