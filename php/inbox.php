<?php
// myday.php
// database.php dosyasını dahil et
require 'database.php';

// Veritabanı bağlantısını oluştur
$database = new Database();
$db = $database->getConnection();

// --- POST isteği geldiyse (ekleme işlemi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    if (!empty($title)) {
        try {
            $query = "INSERT INTO tasks (title) VALUES (:title)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->execute();
        } catch (PDOException $e) {
            http_response_code(500); // Hata durum kodu gönder
            die("Ekleme hatası: " . $e->getMessage());
        }
    }
}

// --- Tüm görevleri (tasks) veritabanından çekme ---
$tasks = [];
try {
    $query = "SELECT id, title, status, created_at FROM tasks WHERE deleted_at IS NULL ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Listeleme hatası: " . $e->getMessage();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

    <section center-column>
    <div class="column-top">
        <div class="column-top-left">
            <ul>
                <li><button><i class="fa-regular fa-house"></i><span class="title">Görevler</span></button></li>
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
                <li>
                    <button id="restore-completed-button">
                    <i class="fa-solid fa-arrows-rotate"></i><span>Geri Getir</span>
                    </button>
                </li>
                <li><button><i class="fa-regular fa-lightbulb"></i><span>Öneriler</span></button></li>
            </ul>
        </div>
        <div class="column-top-left-date">
            <span class="date"><?php echo date('d F Y'); ?></span>
        </div>
    </div>
    <div class="column-bottom">
                <div class="taskCreation">
                <div class="taskCreation-entrybar-left">
                    <ul>
                        <li><button id="calendar-button" title="Takvim"><i class="fa-regular fa-calendar-days"></i></button></li>
                        <li><button id="show-tasks-by-date" title="Teslim tarihine göre veri listesi "><i class="fa-regular fa-bell"></i></button></li>
                    </ul>
                </div>
            </div>
        <form id="taskForm" action="php/myday.php" method="post">
            <div class="add-Task">
                <div class="add-TaskNew">
                    <button type="button">
                        <i class="fa-regular fa-plus"></i>
                    </button>
                    <input type="text" name="title" id="task-input" placeholder="Görev Ekle">
                </div>
            </div>
        </form>
        
        <div class="grid-wiew">
    <div class="grid-wiew-container">
        <div class="grid-container">
            <div class="grid-container-header">
                <ul>
                    <li><span class="completed">
                        <button id="toggle-button">
                            <i id="toggle-icon" class="fa-regular fa-eye"></i>
                         </button>
                    </span></li>
                    <li><span class="title">Adı</span></li>
                    <li><span class="date">Tarih</span></li>
                    <li><span class="importance">Önem Derecesi</span></li>
                    <li><span class="update">Güncele</span></li>
                    <li><span class="clear">Sil</span></li>
                </ul>
            </div>
            <div class="grid-tasks">
    <?php if (empty($tasks)): ?>
        <p style="padding: 10px; text-align: center;">Henüz eklenmiş bir görev yok.</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
             <div class="grid <?php echo ($task['status'] == 'completed') ? 'completed-task' : ''; ?>" data-id="<?php echo htmlspecialchars($task['id']); ?>">
                <ul>
                    <li>
                        <button type="submit" aria-label="completed"><i class="fa-regular fa-circle"></i></button>
                    </li>
                    <li>
                        <span class="title title-text"><?php echo htmlspecialchars($task['title']); ?></span>
                    </li>
                    <li>
                        <span class="date">
                            <?php echo (new DateTime($task['created_at']))->format('d/m/Y H:i'); ?>
                        </span>
                    </li>
                    <li>
                        <button type="submit" aria-label="importance"><i class="fa-regular fa-star"></i></button>
                    </li>
                    <li>
                        <button type="button" class="update-btn" aria-label="Güncelle">Güncelle</button>
                    </li>
                    <li>
                        <button type="button" class="delete-btn" aria-label="Sil">Sil</button>
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

    // Toastr genel ayarları
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // 1. Görev ekleme (Form submit)
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
            success: function() {
                toastr.success('Görev başarıyla eklendi!');
                $('#task-input').val('');
                $('#content-area').load('php/myday.php');
            },
            error: function(xhr, status, error) {
                toastr.error('Görev eklenirken bir hata oluştu: ' + error);
            }
        });
    });

    // 2. Görev başlığı çift tıklama → düzenleme
    $(document).on('dblclick', '.title-text', function() {
        var $titleSpan = $(this);
        var currentTitle = $titleSpan.text();
        var $input = $('<input type="text" class="edit-input" />').val(currentTitle);

        $titleSpan.hide().after($input);
        $input.focus();
    });

    // 3. Güncelle butonu
    $(document).on('click', '.update-btn', function() {
        var $gridDiv = $(this).closest('.grid');
        var taskId = $gridDiv.data('id');
        var $input = $gridDiv.find('.edit-input');

        if ($input.length) {
            var newTitle = $input.val();
            
            if (newTitle.trim() === '') {
                toastr.warning('Görev başlığı boş bırakılamaz.');
                return;
            }

            $.ajax({
                type: "POST",
                url: "php/update_task.php",
                data: { id: taskId, title: newTitle },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $gridDiv.find('.title-text').text(newTitle).show();
                        $input.remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Güncelleme sırasında bir hata oluştu.');
                }
            });
        } else {
            toastr.warning('Lütfen önce başlığı çift tıklayarak düzenleyin.');
        }
    });

    // 4. Düzenleme alanından çıkınca (blur)
    $(document).on('blur', '.edit-input', function() {
        var $input = $(this);
        var newTitle = $input.val();
        var $titleSpan = $input.siblings('.title-text');

        if (newTitle.trim() === '' || newTitle === $titleSpan.text()) {
            $titleSpan.show();
            $input.remove();
        }
    });

    // 5. Silme butonu
    $(document).on('click', '.delete-btn', function() {
        var taskId = $(this).closest('.grid').data('id');
        var taskElement = $(this).closest('.grid');
        
        if (confirm('Bu görevi silmek istediğinizden emin misiniz?')) {
            $.ajax({
                type: "POST",
                url: "php/delete.php",
                data: { id: taskId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        taskElement.remove();
                        if ($('.grid-tasks .grid').length === 0) {
                            $('.grid-tasks').html('<p style="padding: 10px; text-align: center;">Henüz eklenmiş bir görev yok.</p>');
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Görev silinirken bir hata oluştu.');
                }
            });
        }
    });

    // 6. Tamamlanan görevleri gizle/göster
    const toggleIcon = $('#toggle-icon');
    $('#toggle-button').on('click', function() {
        if (toggleIcon.hasClass('fa-eye')) {
            toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            $('.grid.completed-task').show();
            toastr.info("Tamamlananlar açıldı.");
        } else {
            toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            $('.grid.completed-task').hide();
            toastr.info("Tamamlananlar gizlendi.");
        }
    });
    $('.grid.completed-task').hide(); // İlk açılışta gizle

    // 7. Görev tamamlama
    $(document).on('click', 'button[aria-label="completed"]', function() {
        const $btn = $(this);
        const $task = $btn.closest('.grid');
        const taskId = $task.data('id');
        const $title = $task.find('.title-text');

        $.ajax({
            type: "POST",
            url: "php/update_task_status.php",
            data: { task_id: taskId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Görev durumu güncellendi.');
                    $task.toggleClass('completed-task');
                    $title.toggleClass('completed-title');
                    $btn.find('i').toggleClass('fa-circle fa-circle-check');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Sunucuyla iletişim kurulurken hata oluştu.');
            }
        });
    });

    // 8. Başlığa göre sıralama
    let sortAsc = true;
    $('#sort-button').on('click', function(e) {
        e.preventDefault();
        let $tasksContainer = $('.grid-tasks');
        let $tasks = $tasksContainer.children('.grid');

        $tasks.sort(function(a, b) {
            let titleA = $(a).find('.title').text().toLowerCase(); 
            let titleB = $(b).find('.title').text().toLowerCase();
            return sortAsc ? titleA.localeCompare(titleB) : titleB.localeCompare(titleA);
        });

        $tasksContainer.append($tasks);
        sortAsc = !sortAsc;
        toastr.info(sortAsc ? 'Z-A sıralandı' : '.A-Z sıralandı.');
    });

    // 9. Önem derecesi
    $(document).on('click', 'button[aria-label="importance"]', function() {
        const $btn = $(this);
        const $task = $btn.closest('.grid');
        const taskId = $task.data('id');
        const $icon = $btn.find('i');

        $.ajax({
            type: "POST",
            url: "php/update_importance.php",
            data: { task_id: taskId },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    if (response.importance === 'important') {
                        $icon.removeClass('fa-regular').addClass('fa-solid').css('color', '#2564cf');
                        toastr.success('Görev önemli olarak işaretlendi.');
                    } else {
                        $icon.removeClass('fa-solid').addClass('fa-regular').css('color', '');
                        toastr.info('Görev önemli listesinden çıkarıldı.');
                    }
                } else {
                    toastr.error(response.message || 'Bir hata oluştu.');
                }
            },
            error: function() {
                toastr.error('Sunucu ile iletişim kurulamadı.');
            }
        });
    });

    // 10. Flatpickr ile tarih seçme
    $('#taskForm').append('<input type="hidden" name="due_date" id="due-date-input">');
    flatpickr("#calendar-button", {
        enableTime: false,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) {
            $('#due-date-input').val(dateStr);
            toastr.info('Görev için tarih belirlendi: ' + dateStr);
        }
    });

});
// 11. Liste ve Tablo Görünümü butonu olay yöneticileri

$('button:has(.fa-bars-staggered)').on('click', function() {
    $('.grid-tasks').addClass('list-view');
    $('.grid').addClass('list-item');
    toastr.info("Liste görünümüne geçildi.");
});

$('button:has(.fa-table-cells-large)').on('click', function() {
    $('.grid-tasks').removeClass('list-view');
    $('.grid').removeClass('list-item');
    toastr.info("Tablo görünümüne geçildi.");
});
</script>

