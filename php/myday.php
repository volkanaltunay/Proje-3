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
                <li>
                    <button id="restore-completed-button"  title="Tamamlanmış görevleri tekrar listeye ekle">
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
                        <div class="grid <?php echo ($task['status'] == 'completed') ? 'completed-task' : ''; ?>" data-id="<?php echo htmlspecialchars($task['id']); ?>">
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
// Seçtigin tarihteki görevleri listele
$(document).on('click', '#show-tasks-by-date', function() {
    const selectedDate = $('#due-date-input').val();
    
    if (selectedDate) {
        toastr.info(selectedDate + ' tarihine ait görevler listeleniyor...');
        
        $('.grid-tasks').load('php/get_tasks_by_date.php?due_date=' + selectedDate, function(response, status, xhr) {
            if (status === "error") {
                toastr.error('Görevler yüklenirken bir hata oluştu.');
            } else {
                toastr.success(selectedDate + ' tarihine ait görevler başarıyla listelendi.');
            }
        });
    } else {
        toastr.warning('Lütfen önce takvimden bir tarih seçin.');
    }
});
});
    // 7. Görev tamamlama butonu
    $(document).on('click', 'button[aria-label="completed"]', function() {
        const $completedButton = $(this);
        const $taskElement = $completedButton.closest('.grid');
        const taskId = $taskElement.data('id');
        const $titleElement = $taskElement.find('.title-text');
        
        $.ajax({
            type: "POST",
            url: "php/update_task_status.php",
            data: { task_id: taskId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Görev durumu güncellendi.');
                    $taskElement.toggleClass('completed-task');
                    $titleElement.toggleClass('completed-title');
                    
                    if ($taskElement.hasClass('completed-task')) {
                         $completedButton.find('i').removeClass('fa-circle').addClass('fa-circle-check');
                    } else {
                         $completedButton.find('i').removeClass('fa-circle-check').addClass('fa-circle');
                    }
                } else {
                    toastr.error('Görev güncellenirken bir hata oluştu: ' + response.message);
                }
            },
            error: function() {
                toastr.error('Sunucuyla iletişim kurulurken bir hata oluştu.');
            }
        });
    });

    // Sayfa yüklendiğinde tamamlanmış görevlerin gizli olması
    $('.grid.completed-task').hide();
    // 9.Önem derecesi butonu tıklama
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
    // 3. "Güncelle" butonuna tıklandığında çalışacak kod
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
                error: function(xhr, status, error) {
                    toastr.error('Güncelleme sırasında bir hata oluştu.');
                    console.error("Hata:", error);
                }
            });
        } else {
            toastr.warning('Lütfen önce başlığı çift tıklayarak düzenleyin.');
        }
    });

    // 4. Input alanından çıkıldığında (blur)
    $(document).on('blur', '.edit-input', function() {
        var $input = $(this);
        var newTitle = $input.val();
        var $titleSpan = $input.siblings('.title-text');

        if (newTitle.trim() === '' || newTitle === $titleSpan.text()) {
            $titleSpan.show();
            $input.remove();
        }
    });
    
    // 5. Silme butonuna tıklandığında çalışır
    $(document).on('click', '.delete-btn', function() {
        var taskId = $(this).closest('.grid').data('id');
        var taskElement = $(this).closest('.grid');

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "onclick": null
        };
        
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
                error: function(xhr, status, error) {
                    toastr.error('Görev silinirken bir hata oluştu.');
                    console.error("Hata:", error);
                }
            });
        }
    });
$(document).ready(function() {
    // "Geri Getir" butonuna tıklandığında
    $(document).on('click', '#restore-completed-button', function() {
        $.ajax({
            type: "POST",
            url: "php/restore_completed_tasks.php",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Görev listesini yenile
                    $('#content-area').load('php/myday.php');
                } else {
                    toastr.info(response.message);
                }
            },
            error: function() {
                toastr.error('Sunucu ile iletişim kurulamadı.');
            }
        });
    });
});
</script>