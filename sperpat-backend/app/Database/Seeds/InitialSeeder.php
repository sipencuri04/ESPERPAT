<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // Create Superuser
        $this->db->table('users')->insert([
            'name'               => 'Super Admin',
            'email'              => 'superuser@esperpat.com',
            'password'           => password_hash('admin123', PASSWORD_BCRYPT),
            'phone'              => '081234567890',
            'role'               => 'superuser',
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'is_active'          => 1,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // Create Admin
        $this->db->table('users')->insert([
            'name'               => 'Admin Toko',
            'email'              => 'admin@esperpat.com',
            'password'           => password_hash('admin123', PASSWORD_BCRYPT),
            'phone'              => '081234567891',
            'role'               => 'admin',
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'is_active'          => 1,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // Create Customer
        $this->db->table('users')->insert([
            'name'               => 'Customer Demo',
            'email'              => 'customer@esperpat.com',
            'password'           => password_hash('customer123', PASSWORD_BCRYPT),
            'phone'              => '081234567892',
            'address'            => 'Jl. Demo No. 1, Jakarta',
            'role'               => 'customer',
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'is_active'          => 1,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // Categories
        $categories = [
            ['name' => 'Mesin & Engine', 'slug' => 'mesin-engine', 'description' => 'Sparepart mesin dan engine kendaraan'],
            ['name' => 'Rem & Brake', 'slug' => 'rem-brake', 'description' => 'Komponen rem kendaraan'],
            ['name' => 'Suspensi', 'slug' => 'suspensi', 'description' => 'Shockbreaker dan komponen suspensi'],
            ['name' => 'Kelistrikan', 'slug' => 'kelistrikan', 'description' => 'Komponen kelistrikan kendaraan'],
            ['name' => 'Body & Eksterior', 'slug' => 'body-eksterior', 'description' => 'Part body dan eksterior kendaraan'],
            ['name' => 'Oli & Pelumas', 'slug' => 'oli-pelumas', 'description' => 'Oli mesin, transmisi, dan pelumas'],
            ['name' => 'Filter', 'slug' => 'filter', 'description' => 'Filter oli, udara, AC, dan bahan bakar'],
            ['name' => 'Aksesoris', 'slug' => 'aksesoris', 'description' => 'Aksesoris kendaraan'],
        ];

        foreach ($categories as $cat) {
            $cat['created_at'] = date('Y-m-d H:i:s');
            $cat['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->insert($cat);
        }

        // Products
        $products = [
            // Mesin & Engine (category_id: 1)
            ['name' => 'Piston Kit Honda Vario 150', 'slug' => 'piston-kit-honda-vario-150-' . time(), 'category_id' => 1, 'harga_beli' => 185000, 'harga_jual' => 250000, 'stok' => 25, 'deskripsi' => 'Piston kit original untuk Honda Vario 150. Termasuk ring piston dan pin piston.'],
            ['name' => 'Kampas Kopling Racing Yamaha', 'slug' => 'kampas-kopling-racing-yamaha-' . time(), 'category_id' => 1, 'harga_beli' => 95000, 'harga_jual' => 145000, 'stok' => 30, 'deskripsi' => 'Kampas kopling racing untuk motor Yamaha series. Material kevlar tahan panas.'],
            ['name' => 'Cylinder Block Suzuki Satria FU', 'slug' => 'cylinder-block-suzuki-satria-fu-' . time(), 'category_id' => 1, 'harga_beli' => 450000, 'harga_jual' => 650000, 'stok' => 10, 'deskripsi' => 'Cylinder block aftermarket berkualitas untuk Suzuki Satria FU 150.'],
            
            // Rem & Brake (category_id: 2)
            ['name' => 'Disc Brake Pad Brembo Universal', 'slug' => 'disc-brake-pad-brembo-universal-' . time(), 'category_id' => 2, 'harga_beli' => 120000, 'harga_jual' => 180000, 'stok' => 50, 'deskripsi' => 'Kampas rem cakram Brembo universal untuk motor dan mobil. Daya pengereman maksimal.'],
            ['name' => 'Kaliper Rem Racing CNC', 'slug' => 'kaliper-rem-racing-cnc-' . time(), 'category_id' => 2, 'harga_beli' => 350000, 'harga_jual' => 520000, 'stok' => 15, 'deskripsi' => 'Kaliper rem racing 2 piston CNC machined. Finish anodized merah.'],
            ['name' => 'Master Rem Belakang Universal', 'slug' => 'master-rem-belakang-universal-' . time(), 'category_id' => 2, 'harga_beli' => 75000, 'harga_jual' => 120000, 'stok' => 40, 'deskripsi' => 'Master rem belakang aftermarket universal untuk motor matic dan bebek.'],
            
            // Suspensi (category_id: 3)
            ['name' => 'Shockbreaker Kayaba Honda Beat', 'slug' => 'shockbreaker-kayaba-honda-beat-' . time(), 'category_id' => 3, 'harga_beli' => 280000, 'harga_jual' => 385000, 'stok' => 20, 'deskripsi' => 'Shockbreaker belakang Kayaba original untuk Honda Beat. Nyaman dan awet.'],
            ['name' => 'Per Klep Racing TDR', 'slug' => 'per-klep-racing-tdr-' . time(), 'category_id' => 3, 'harga_beli' => 45000, 'harga_jual' => 75000, 'stok' => 35, 'deskripsi' => 'Per klep racing TDR. Meningkatkan performa valve kendaraan.'],
            
            // Kelistrikan (category_id: 4)
            ['name' => 'CDI Racing BRT Yamaha Jupiter', 'slug' => 'cdi-racing-brt-yamaha-jupiter-' . time(), 'category_id' => 4, 'harga_beli' => 250000, 'harga_jual' => 380000, 'stok' => 18, 'deskripsi' => 'CDI racing BRT dual band untuk Yamaha Jupiter series. Timing adjustable.'],
            ['name' => 'Lampu LED H4 Philips 6000K', 'slug' => 'lampu-led-h4-philips-6000k-' . time(), 'category_id' => 4, 'harga_beli' => 160000, 'harga_jual' => 250000, 'stok' => 45, 'deskripsi' => 'Lampu LED H4 Philips putih 6000K. Terang tanpa silau. Original resmi.'],
            ['name' => 'Aki GS Astra MF 5Ah', 'slug' => 'aki-gs-astra-mf-5ah-' . time(), 'category_id' => 4, 'harga_beli' => 175000, 'harga_jual' => 265000, 'stok' => 30, 'deskripsi' => 'Aki maintenance free GS Astra 12V 5Ah. Cocok untuk motor matic.'],
            
            // Oli & Pelumas (category_id: 6)
            ['name' => 'Oli Mesin Motul 5100 10W-40 1L', 'slug' => 'oli-mesin-motul-5100-10w40-1l-' . time(), 'category_id' => 6, 'harga_beli' => 85000, 'harga_jual' => 130000, 'stok' => 60, 'deskripsi' => 'Oli mesin synthetic blend Motul 5100 10W-40 untuk motor 4-tak. Volume 1 Liter.'],
            ['name' => 'Oli Gardan Yamalube 140ml', 'slug' => 'oli-gardan-yamalube-140ml-' . time(), 'category_id' => 6, 'harga_beli' => 22000, 'harga_jual' => 35000, 'stok' => 80, 'deskripsi' => 'Oli gardan original Yamalube untuk motor matic Yamaha. Volume 140ml.'],
            
            // Filter (category_id: 7)
            ['name' => 'Filter Udara K&N Racing', 'slug' => 'filter-udara-kn-racing-' . time(), 'category_id' => 7, 'harga_beli' => 200000, 'harga_jual' => 310000, 'stok' => 20, 'deskripsi' => 'Filter udara racing K&N washable. Dapat dicuci dan digunakan kembali.'],
            ['name' => 'Filter Oli Denso Honda', 'slug' => 'filter-oli-denso-honda-' . time(), 'category_id' => 7, 'harga_beli' => 28000, 'harga_jual' => 45000, 'stok' => 100, 'deskripsi' => 'Filter oli element Denso untuk motor Honda. Original quality.'],
            
            // Aksesoris (category_id: 8)
            ['name' => 'Knalpot Racing R9 Misano Yamaha', 'slug' => 'knalpot-racing-r9-misano-yamaha-' . time(), 'category_id' => 8, 'harga_beli' => 850000, 'harga_jual' => 1250000, 'stok' => 8, 'deskripsi' => 'Knalpot racing R9 Misano full system untuk Yamaha R15/MT-15. Full stainless steel.'],
            ['name' => 'Handguard Acerbis Rally Pro', 'slug' => 'handguard-acerbis-rally-pro-' . time(), 'category_id' => 8, 'harga_beli' => 180000, 'harga_jual' => 285000, 'stok' => 12, 'deskripsi' => 'Handguard Acerbis Rally Pro universal. Proteksi tangan dari angin dan debris.'],
        ];

        foreach ($products as $prod) {
            $prod['is_active']  = 1;
            $prod['created_at'] = date('Y-m-d H:i:s');
            $prod['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('products')->insert($prod);
        }
    }
}
