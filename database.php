<?php

// .env dosyasını okumak için basit bir fonksiyon
function get_env($key) {
    static $env = null;
    if ($env === null) {
        // Projenizin kök dizinine giden yolu manuel olarak belirtin.
        // WAMP'ta bu genellikle C:\wamp64\www\Proje-3\ şeklindedir.
        $project_root = "C:/wamp64/www/Proje-3/"; 
        $envFilePath = $project_root . '.env';

        if (!file_exists($envFilePath)) {
            die("Hata: .env dosyası bulunamadı. Lütfen dosya yolunu kontrol edin: " . $envFilePath);
        }
        $env = parse_ini_file($envFilePath);
    }
    return $env[$key] ?? null;
}

// .env dosyasından bağlantı bilgilerini al
$host = get_env('DB_HOST');
$user = get_env('DB_USER');
$pass = get_env('DB_PASS');
$dbname = get_env('DB_NAME');

// İlk bağlantı denemesi, veritabanı olmadan
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Veritabanını oluştur
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Veritabanı '$dbname' başarıyla oluşturuldu veya zaten mevcut.<br>";

    // Oluşturulan veritabanına bağlan
    $pdo->exec("USE `$dbname`");

    // tasks tablosunu oluştur
    $sql = "CREATE TABLE IF NOT EXISTS `tasks` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `status` ENUM('pending', 'completed') DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL
    )";
    $pdo->exec($sql);

    echo "Veritabanı ve 'tasks' tablosu başarıyla oluşturuldu.<br>";

} catch (PDOException $e) {
    die("Veritabanı bağlantı veya oluşturma hatası: " . $e->getMessage());
}
?>