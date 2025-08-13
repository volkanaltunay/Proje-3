document.addEventListener("DOMContentLoaded", function () {
    const taskInput = document.querySelector(".add-TaskNew input");
    const addButton = document.querySelector(".taskCreation-entrybar-right button");
    const gridContainer = document.querySelector(".grid-wiew-container");

    // LocalStorage'dan görevleri yükle
    loadTasks();

    // Görev ekleme butonuna tıklanınca
    addButton.addEventListener("click", function () {
        const taskName = taskInput.value.trim();
        if (taskName === "") {
            if (typeof toastr !== 'undefined') {
                toastr.warning("Lütfen bir görev adı girin");
            }
            return;
        }

        const newTask = {
            title: taskName,
            date: getTodayDate(),
            importance: false,
            completed: false
        };

        // Görevi kaydet
        saveTask(newTask);

        // Görevi ekranda göster
        addTaskRow(newTask);

        // Input temizle
        taskInput.value = "";

        if (typeof toastr !== 'undefined') {
            toastr.success("Görev eklendi");
        }
    });

    // Görev satırı oluşturma
    function addTaskRow(task) {
        const newRow = document.createElement("div");
        newRow.classList.add("grid");
        newRow.innerHTML = `
            <ul>
                <li>
                  <button class="completed"><i class="fa-regular fa-circle"></i></button>
                </li>
                <li><span class="title">${task.title}</span></li>
                <li><span class="date">${task.date}</span></li>
                <li><span class="importance"><i class="fa-solid fa-star ${task.importance ? 'active' : ''}"></i></span></li>
            </ul>
        `;
        gridContainer.appendChild(newRow);
    }

    // Görevleri localStorage'a kaydet
    function saveTask(task) {
        const tasks = JSON.parse(localStorage.getItem("tasks")) || [];
        tasks.push(task);
        localStorage.setItem("tasks", JSON.stringify(tasks));
    }

    // localStorage'dan görevleri yükle
    function loadTasks() {
        const tasks = JSON.parse(localStorage.getItem("tasks")) || [];
        tasks.forEach(task => addTaskRow(task));
    }

    // Bugünün tarihini döndür
    function getTodayDate() {
        const months = ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];
        const today = new Date();
        return today.getDate() + " " + months[today.getMonth()] + " " + today.getFullYear();
    }
});



