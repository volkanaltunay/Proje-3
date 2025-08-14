document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggle-btn");
    const sidebar = document.getElementById("sidebar");
    const links = document.querySelectorAll('#sidebar a[data-page]');
    const contentArea = document.getElementById("content-area");

    // Menü aç/kapa
    toggleBtn.addEventListener("click", function (e) {
        e.preventDefault();
        sidebar.classList.toggle("collapsed");
    });

    // Menüye tıklayınca içerik yükle
    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const pageUrl = this.getAttribute("data-page");

            fetch(pageUrl)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Sunucu hatası: ${res.status}`);
                    }
                    return res.text();
                })
                .then(html => {
                    contentArea.innerHTML = html;
                    // Eğer toastr tanımlıysa çalışacak
                    if (typeof toastr !== 'undefined') {
                        toastr.success("Sayfa başarıyla yüklendi");
                    }
                })
                .catch(err => {
                    // Eğer toastr tanımlıysa çalışacak
                    if (typeof toastr !== 'undefined') {
                        toastr.error("Sayfa yüklenirken bir hata oluştu");
                    }
                    console.error(err);
                });
        });
    });
});
