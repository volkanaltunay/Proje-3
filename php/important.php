<?php
require 'database.php';
$database = new Database();
$db = $database->getConnection();

// Sadece önemli görevleri al
$tasks = [];
try {
    $query = "SELECT id, title, status, importance, created_at 
              FROM tasks 
              WHERE importance = 'important' AND deleted_at IS NULL 
              ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Listeleme hatası: " . $e->getMessage();
}
?>
<section center-column>
    <div class="column-top">
        <div class="column-top-left">
            <ul>
                <li>
                    <button><i class="fa-solid fa-star" ></i>
                        <span class="title">Önemli</span>
                    </button>
                </li>
                   <li><button><i class="fa-solid fa-ellipsis"></i></button></li>
                <li><button><i class="fa-solid fa-table-cells-large"></i><span>Tablo</span></button></li>
                <li><button><i class="fa-solid fa-bars-staggered"></i><span>Liste</span></button></li>
            </ul>
        </div>
             <div class="column-top-right">
            <ul>
                <li>
                    <button id="sort-button">
                    <i class="fa-solid fa-arrow-down-a-z"></i><span>Sırala</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="column-bottom">
        <div class="grid-wiew">
            <div class="grid-wiew-container">
                <div class="grid-container">
                    <div class="grid-container-header">
                        <ul>
                            <li><span class="completed"></span></li>
                            <li><span class="title">Adı</span></li>
                            <li><span class="date">Tarih</span></li>
                            <li><span class="importance">Önem Derecesi</span></li>
                        </ul>
                    </div>
                    <div class="grid-tasks">
                        <?php if (empty($tasks)): ?>
                            <p style="padding: 10px; text-align: center;">
                                Henüz önemli olarak işaretlenmiş bir görev yok.
                            </p>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <div class="grid <?php echo ($task['status'] == 'completed') ? 'completed-task' : ''; ?>" 
                                     data-id="<?php echo htmlspecialchars($task['id']); ?>">
                                    <ul>
                                        <li>
                                            <button type="button" class="completed-btn" aria-label="completed">
                                                <i class="<?php echo ($task['status'] == 'completed') 
                                                    ? 'fa-solid fa-circle-check' 
                                                    : 'fa-regular fa-circle'; ?>"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <span class="title title-text">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="date">
                                                <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                                            </span>
                                        </li>
                                        <li>
                                            <button type="button" class="importance-btn" aria-label="importance">
                                                <i class="fa-solid fa-star" style="color:#2564cf"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
        $(document).ready(function() {
    let sortAsc = true; 

    $('#sort-button').on('click', function(e) {
        e.preventDefault();
        
        let $tasksContainer = $('.grid-tasks');
        let $tasks = $tasksContainer.children('.grid');


        $tasks.sort(function(a, b) {
            let titleA = $(a).find('.title').text().toLowerCase(); 
            let titleB = $(b).find('.title').text().toLowerCase();
            
            if (titleA < titleB) return sortAsc ? -1 : 1;
            if (titleA > titleB) return sortAsc ? 1 : -1;
            return 0;
        });


        $tasksContainer.append($tasks);


        sortAsc = !sortAsc;

        toastr.info(sortAsc ? 'Z-A sıralandı.' : 'A-Z sıralandı.');
    });
});
</script>


