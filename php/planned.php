<?php
// PHP kodları burada çalıştırılabilir
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    #content-area {
  margin-left: 150px;
  transition: margin-left 0.3s ease;
}
</style>
<body>
<!-- Sağ İçerik Alanı -->
<main id="content-area">
      <section center-column>
        <div class="column-top">
          <div class="column-top-left">
            <ul>
              <li>
                <button>
                  <i class="fa-solid fa-calendar-days"></i>
                  <span class="title">Planlanan</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-ellipsis"></i>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-table-cells-large"></i>
                  <span>Tablo</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-bars-staggered"></i>
                  <span>Liste</span>
                </button>
              </li>
            </ul>
          </div>
          <div class="column-top-right">
            <ul>
              <li>
                <button>
                  <i class="fa-solid fa-arrow-down-a-z"></i>
                  <span>Sırala</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-solid fa-layer-group"></i>
                  <span>Grup</span>
                </button>
              </li>
              <li>
                <button>
                  <i class="fa-regular fa-lightbulb"></i>
                  <span>Öneriler</span>
                </button>
              </li>
            </ul>
          </div>
          <div class="column-top-left-date">
            <span class="date">13 Ağustos Çarşamba</span>
          </div>
        </div>
        <div class="column-bottom">
          <div class="add-Task">
            <div class="add-TaskNew">
              <button>
                <i class="fa-regular fa-circle"></i>
              </button>
              <input type="text" placeholder="Görev Ekle">
            </div>
          </div>
          <div class="taskCreation">
            <div class="taskCreation-entrybar-left">
              <ul>
                <li>
                <button>
                  <i class="fa-solid fa-calendar-days"></i>
                </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-bell"></i>
                  </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-repeat"></i>
                  </button>
                </li>
              </ul>
            </div>
            <div class="taskCreation-entrybar-right">
              <button aria-label="Ekle">Ekle</button>
            </div>
          </div>
        </div>
      </section>
    </main>
</body>
</html>