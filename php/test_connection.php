<?php

require 'database.php';

// Database sınıfından bir nesne oluştur
$database = new Database();

// getConnection metodunu çağırarak bağlantıyı al
$db = $database->getConnection();

// Bağlantının başarılı olup olmadığını kontrol et
if ($db) {
    echo "Veritabanı bağlantısı başarıyla kuruldu!";
} else {
    echo "Veritabanı bağlantısı kurulamadı.";
}

?>