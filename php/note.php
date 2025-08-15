
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color: #222;
            color: #fff;
            overflow: hidden;
        }

        main {
            width: 100vw;
            height: 100vh;
            background-image: repeating-linear-gradient(to right, transparent 0 50px, #fff1 50px 51px),
            repeating-linear-gradient(to bottom, transparent 0 50px, #fff1 50px 51px);
            position: relative;
        }

        #note-form {
            background-color: #ffffffff;
            width: max-content;
            padding: 5px;
            margin: 10px;
            border-radius: 24px;
            display: flex;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #note-form input, #note-form button {
            width: 30px;
            height: 30px;
            padding: 0;
            border: none;
            background-color: transparent;
            font-size: large;
            cursor: pointer;
        }

        #note-form input::-webkit-color-swatch-wrapper {
            padding: 0;
        }
        #note-form input::-webkit-color-swatch {
            border-radius: 50%;
        }

        #list .note {
            background-color: #333;
            width: 250px;
            height: 250px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
            padding: 10px;
            position: absolute; /* Bu satır çok önemli! */
            top: 60px;
            left: 20px;
            resize: both;
            overflow: hidden;
            border-top: 30px solid #e6b905; /* Örnek renk */
            cursor: grab; /* Başlangıç imleci */
        }

        #list .note textarea {
            all: unset;
            width: 100%;
            height: 100%;
            color: #d6d6d6;
            padding: 5px;
            box-sizing: border-box;
        }

        #list .note .close {
            position: absolute;
            top: 5px;
            right: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: large;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main>
        <form id="note-form">
            <input type="color" id="color" value="#e6b905">
            <button type="button" id="createBtn">+</button>
        </form>
        <div id="list">
            </div>
    </main>

    <script>
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
        });
    </script>
