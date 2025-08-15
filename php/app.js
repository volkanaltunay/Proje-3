document.addEventListener("DOMContentLoaded", () => {
    const colorInput = document.getElementById('color');
    const createBtn = document.getElementById('createBtn');
    const noteList = document.getElementById('list');

    createBtn.addEventListener('click', () => {
        const newNote = document.createElement('div');
        newNote.classList.add('note');
        newNote.innerHTML = `
            <span class="close">x</span>
            <textarea placeholder="İçerik yazın..."></textarea>
        `;
        newNote.style.borderColor = colorInput.value;
        noteList.appendChild(newNote);
    });

    // Not silme işlevi
    noteList.addEventListener('click', (event) => {
        if (event.target.classList.contains('close')) {
            event.target.parentNode.remove();
        }
    });

    // Sürükle-Bırak işlevleri için değişkenler
    let draggedNote = null;
    let offsetX = 0;
    let offsetY = 0;

    // Sürükleme başlangıcı (mousedown)
    noteList.addEventListener('mousedown', (event) => {
        // Notun üst kısmına tıklanıyorsa sürüklemeyi başlat
        if (event.target.classList.contains('note')) {
            draggedNote = event.target;
            draggedNote.style.cursor = 'grabbing';
            draggedNote.style.zIndex = '1000'; // En öne getir

            // İmleç ve not arasındaki farkı hesapla
            offsetX = event.clientX - draggedNote.getBoundingClientRect().left;
            offsetY = event.clientY - draggedNote.getBoundingClientRect().top;
        }
    });

    // Sürükleme devamı (mousemove)
    document.addEventListener('mousemove', (event) => {
        if (draggedNote === null) return;

        event.preventDefault(); // Metin seçilmesini engelle
        
        // Notun yeni konumunu hesapla ve ayarla
        draggedNote.style.left = `${event.clientX - offsetX}px`;
        draggedNote.style.top = `${event.clientY - offsetY}px`;
    });

    // Sürükleme sonu (mouseup)
    document.addEventListener('mouseup', () => {
        if (draggedNote === null) return;

        draggedNote.style.cursor = 'grab';
        draggedNote.style.zIndex = 'auto';
        draggedNote = null; // Seçimi sıfırla
    });
});