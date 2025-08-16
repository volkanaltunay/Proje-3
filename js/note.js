function initNotes() {
    const colorInput = document.getElementById('color');
    const createBtn = document.getElementById('createBtn');
    const noteList = document.getElementById('list');

    if (!colorInput || !createBtn || !noteList) return; // henüz yüklenmemişse çık

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

    noteList.addEventListener('click', (event) => {
        if (event.target.classList.contains('close')) {
            event.target.parentNode.remove();
        }
    });

    let draggedNote = null;
    let offsetX = 0;
    let offsetY = 0;

    noteList.addEventListener('mousedown', (event) => {
        if (event.target.classList.contains('note')) {
            draggedNote = event.target;
            draggedNote.style.cursor = 'grabbing';
            draggedNote.style.zIndex = '1000';
            offsetX = event.clientX - draggedNote.getBoundingClientRect().left;
            offsetY = event.clientY - draggedNote.getBoundingClientRect().top;
        }
    });

    document.addEventListener('mousemove', (event) => {
        if (draggedNote === null) return;
        event.preventDefault();
        draggedNote.style.left = `${event.clientX - offsetX}px`;
        draggedNote.style.top = `${event.clientY - offsetY}px`;
    });

    document.addEventListener('mouseup', () => {
        if (draggedNote === null) return;
        draggedNote.style.cursor = 'grab';
        draggedNote.style.zIndex = 'auto';
        draggedNote = null;
    });
}
