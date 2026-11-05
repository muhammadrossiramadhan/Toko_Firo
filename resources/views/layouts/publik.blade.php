<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Firo')</title>
    <meta name="description" content="@yield('meta_description', 'Toko Firo - Toko ATK dan fotocopy di Kraksaan, Probolinggo')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-ink antialiased min-h-screen flex flex-col">
    @include('components.navbar-publik')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer-publik')

    @stack('scripts')

<!-- Chatbot UI -->
<div style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 50; display: flex; flex-direction: column; align-items: flex-end;">
    <!-- Modal Chat -->
    <div id="ai-modal" class="bg-white border border-gray-300 shadow-2xl hidden flex-col rounded-none mb-4" style="display: none; height: 400px; width: 320px; max-width: 90vw;">
        <div class="bg-emerald-800 text-white p-3 font-bold text-center rounded-none tracking-wide flex justify-between items-center">
            <span>Asisten Toko Firo</span>
            <button id="ai-close" class="text-white hover:text-gray-200 font-bold px-2 py-1">X</button>
        </div>
        <div id="ai-history" class="p-3 overflow-y-auto flex flex-col gap-3 bg-gray-50 text-sm" style="flex: 1;">
            <div class="bg-gray-200 text-gray-800 p-3 self-start rounded-none border border-gray-300 max-w-[85%]">
                Halo! Ada yang bisa saya bantu terkait stok atau katalog Toko Firo?
            </div>
        </div>
        <div class="border-t border-gray-300 p-2 flex bg-white">
            <input type="text" id="ai-input" class="flex-1 border border-gray-400 p-2 rounded-none focus:outline-none text-gray-800" placeholder="Ketik pesan...">
            <button id="ai-send" class="bg-emerald-700 text-white px-4 py-2 ml-2 rounded-none font-bold min-h-[2.75rem]">
                Kirim
            </button>
        </div>
    </div>

    <button id="ai-trigger" class="bg-emerald-700 text-white font-bold px-5 py-3 shadow-lg min-h-[2.75rem] min-w-[2.75rem] rounded-none hover:bg-emerald-800 transition-colors">
        Tanya AI
    </button>
</div>

<script>
    const trigger = document.getElementById('ai-trigger');
    const modal = document.getElementById('ai-modal');
    const closeBtn = document.getElementById('ai-close');
    const input = document.getElementById('ai-input');
    const send = document.getElementById('ai-send');
    const history = document.getElementById('ai-history');

    // Toggle Modal
    function toggleChatModal() {
        const isHidden = modal.style.display === 'none' || modal.classList.contains('hidden');
        if (isHidden) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            input.focus();
        } else {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    trigger.addEventListener('click', toggleChatModal);
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }

    // Tambah Balon Chat
    function appendMessage(text, isUser) {
        const div = document.createElement('div');
        div.className = isUser 
            ? 'bg-emerald-100 text-emerald-900 p-3 self-end rounded-none border border-emerald-300 max-w-[85%]'
            : 'bg-gray-200 text-gray-800 p-3 self-start rounded-none border border-gray-300 max-w-[85%]';
        div.textContent = text; // Mencegah XSS
        history.appendChild(div);
        history.scrollTop = history.scrollHeight;
    }

    // Kirim Pesan
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        input.disabled = true;
        send.disabled = true;
        
        appendMessage(text, true);
        appendMessage('Sedang mencari...', false);
        
        try {
            const response = await fetch('/api/chatbot/tanya', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ message: text })
            });
            
            const data = await response.json();
            history.lastChild.remove(); // Hapus teks loading
            appendMessage(data.reply || data.jawaban || 'Maaf, terjadi kesalahan.', false);
        } catch (error) {
            history.lastChild.remove();
            appendMessage('Gagal terhubung ke server.', false);
        } finally {
            input.disabled = false;
            send.disabled = false;
            input.focus();
        }
    }

    send.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
</script>
</body>
</html>
