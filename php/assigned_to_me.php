<?php
// myday.php
// database.php dosyasını dahil et
require 'database.php';

// Veritabanı bağlantısını oluştur
$database = new Database();
$db = $database->getConnection();
?>
<section center-column>
    <div class="column-top">
        <div class="column-top-left">
            <ul>
                <li><button><i class="fa-regular fa-user"></i><span class="title">Bana atanmış</span></button></li>
                <li><button><i class="fa-solid fa-ellipsis"></i></button></li>
                <li><button><i class="fa-solid fa-table-cells-large"></i><span>Tablo</span></button></li>
                <li><button><i class="fa-solid fa-bars-staggered"></i><span>Liste</span></button></li>
            </ul>
        </div>
    </div>
</section>

