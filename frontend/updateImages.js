import mysql from 'mysql2/promise';

async function main() {
    const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        database: 'esperpat',
        password: ''
    });

    const [rows] = await connection.execute('SELECT id, name FROM products');
    for (const row of rows) {
        console.log(row.id, row.name);
    }
    await connection.end();
}
main().catch(console.error);
