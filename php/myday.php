<?php
// myday.php

require 'database.php';
$database = new Database();
$db = $database->getConnection();

// --- POST isteği geldiyse (ekleme işlemi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    $dueDate = isset($_POST['due_date']) && !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if (!empty($title)) {
        try {
            // due_date alanını da ekle
            $query = "INSERT INTO tasks (title, due_date) VALUES (:title, :due_date)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $title);
            if ($dueDate === null) {
                $stmt->bindParam(':due_date', $dueDate, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':due_date', $dueDate, PDO::PARAM_STR);
            }
            $stmt->execute();
            // Görev başarıyla eklendiğinde hiçbir şey yazdırmıyoruz ki Ajax success alsın
        } catch (PDOException $e) {
            http_response_code(500);
            die("Ekleme hatası: " . $e->getMessage());
        }
    }
}

// --- Tüm görevleri (tasks) veritabanından çekme ---
$tasks = [];
try {
    // Sorguya due_date alanını da dahil et
    $query = "SELECT id, title, status, importance, created_at, due_date FROM tasks WHERE deleted_at IS NULL ORDER BY created_at DESC";
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
                <li><button><i class="fa-regular fa-sun"></i><span class="title">Günüm</span></button></li>
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
                <li><button><i class="fa-solid fa-layer-group"></i><span>Grup</span></button></li>
                <li><button><i class="fa-regular fa-lightbulb"></i><span>Öneriler</span></button></li>
            </ul>
        </div>
        <div class="column-top-left-date">
            <span class="date"><?php echo date('d F Y'); ?></span>
        </div>
    </div>
    <div class="column-bottom">
        <form id="taskForm" action="php/myday.php" method="post">
            <div class="add-Task">
                <div class="add-TaskNew">
                    <button type="button">
                        <i class="fa-regular fa-plus"></i>
                    </button>
                    <input type="text" name="title" id="task-input" placeholder="Görev Ekle">
                </div>
            </div>
            <div class="taskCreation">
                <div class="taskCreation-entrybar-left">
                    <ul>
                        <li><button id="calendar-button"><i class="fa-regular fa-calendar-days"></i></button></li>
                        <li><button><i class="fa-regular fa-bell"></i></button></li>
                        <li><button><i class="fa-solid fa-repeat"></i></button></li>
                    </ul>
                </div>
                <div class="taskCreation-entrybar-right">
                    <button type="submit" aria-label="Ekle">Ekle</button>
                </div>
            </div>
        </form>
        
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
                    <p style="padding: 10px; text-align: center;">Henüz eklenmiş bir görev yok.</p>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <div class="grid">
                            <ul>
                                <li>
                                    <button type="submit" aria-label="completed"><i class="fa-regular fa-circle"></i></button>
                                </li>
                                <li>
                                    <span class="title"><?php echo htmlspecialchars($task['title']); ?></span>
                                </li>
                                <li>
                                    <span class="date">
                                        <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                                    </span>
                                </li>
                                <li>
                                    <button type="submit" aria-label="importance"><i class="fa-regular fa-star"></i></button>
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
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    $('#taskForm').append('<input type="hidden" name="due_date" id="due-date-input">');

    flatpickr("#calendar-button", {
        enableTime: false,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            $('#due-date-input').val(dateStr);
            toastr.info('Görev için tarih belirlendi: ' + dateStr);
        }
    });

    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        var taskTitle = $('#task-input').val();

        if (taskTitle.trim() === '') {
            toastr.warning('Lütfen bir görev başlığı girin.');
            return;
        }

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function(response) {
                toastr.success('Görev başarıyla eklendi!');
                $('#task-input').val('');
                $('#content-area').load('php/myday.php');
            },
            error: function(xhr, status, error) {
                toastr.error('Görev eklenirken bir hata oluştu: ' + error);
            }
        });

});
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
});

</script>