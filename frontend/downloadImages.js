import mysql from 'mysql2/promise';
import fs from 'fs';
import path from 'path';
import https from 'https';

const imageMap = {
    2: 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600', // Kampas Kopling
    3: 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=600', // Cylinder Block
    4: 'https://plus.unsplash.com/premium_photo-1664298150346-bf5f2b8f8040?w=600', // Disc Brake
    5: 'https://images.unsplash.com/photo-1549417855-83e3518eabc3?w=600', // Kaliper Rem
    6: 'https://images.unsplash.com/photo-1563728637651-87a38753229b?w=600', // Master Rem
    7: 'https://plus.unsplash.com/premium_photo-1664303358087-4ee4a4087994?w=600', // Shockbreaker
    8: 'https://images.unsplash.com/photo-1620023648601-38fe5fc9793f?w=600', // Per Klep
    9: 'https://images.unsplash.com/photo-1506820257088-348df0807b5a?w=600', // CDI
    10: 'https://images.unsplash.com/photo-1601235317770-4dcb1fada9a3?w=600', // Lampu LED
    11: 'https://images.unsplash.com/photo-1554286694-0cfbcf096a60?w=600', // Aki
    12: 'https://images.unsplash.com/photo-1617467657257-19e09d17ed61?w=600', // Oli Mesin
    13: 'https://images.unsplash.com/photo-1590483489839-4ab5aef06ec2?w=600', // Oli Gardan
    14: 'https://images.unsplash.com/photo-1552504953-61b6062f6db4?w=600', // Filter Udara
    15: 'https://images.unsplash.com/photo-1558981420-c532902e58b4?w=600', // Filter Oli
    16: 'https://images.unsplash.com/photo-1598282367500-4e318ce77e8a?w=600', // Knalpot
    17: 'https://images.unsplash.com/photo-1601445638532-3c6f6c3aa1d6?w=600'  // Handguard
};

const download = (url, dest) => new Promise((resolve, reject) => {
    const file = fs.createWriteStream(dest);
    https.get(url, (response) => {
        if (response.statusCode === 301 || response.statusCode === 302) {
            return download(response.headers.location, dest).then(resolve).catch(reject);
        }
        response.pipe(file);
        file.on('finish', () => {
            file.close(resolve);
        });
    }).on('error', (err) => {
        fs.unlink(dest, () => reject(err));
    });
});

async function main() {
    const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        database: 'esperpat',
    });

    const uploadDir = path.join('D:', 'laragon', 'www', 'ESPERPAT', 'backend', 'public', 'uploads', 'products');
    if (!fs.existsSync(uploadDir)) {
        fs.mkdirSync(uploadDir, { recursive: true });
    }

    for (const [id, url] of Object.entries(imageMap)) {
        const filename = `product_${id}_${Date.now()}.jpg`;
        const dest = path.join(uploadDir, filename);
        await download(url, dest);
        console.log(`Downloaded ${id} to ${filename}`);

        await connection.execute('UPDATE products SET image = ? WHERE id = ?', [`uploads/products/${filename}`, id]);
    }

    await connection.end();
    console.log("Done updating images!");
}

main().catch(console.error);
