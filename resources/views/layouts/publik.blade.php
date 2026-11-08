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

    <main>
        @yield('content')
    </main>

    @include('components.footer-publik')

    @stack('scripts')

<!-- Chatbot UI (Floating Kanan Bawah) -->
<div style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; align-items: flex-end;">
    
    <!-- Modal Chat -->
    <div id="ai-modal" style="display: none; height: 400px; width: 320px; max-width: 90vw; background-color: white; border: 1px solid #d1d5db; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); flex-direction: column; margin-bottom: 1rem; z-index: 10000; border-radius: 0;">
        <div style="background-color: #065f46; color: white; padding: 12px; font-weight: bold; text-align: center; display: flex; justify-content: space-between; align-items: center; border-radius: 0;">
            <span>Asisten Toko Firo</span>
            <button id="ai-close" style="background: none; border: none; color: white; font-weight: bold; cursor: pointer; padding: 4px 8px;">X</button>
        </div>
        <div id="ai-history" style="padding: 12px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background-color: #f9fafb; flex: 1; font-size: 14px;">
            <div style="background-color: #e5e7eb; color: #1f2937; padding: 12px; align-self: flex-start; border: 1px solid #d1d5db; max-width: 85%; border-radius: 0;">
                Halo! Ada yang bisa saya bantu terkait stok atau katalog Toko Firo?
            </div>
        </div>
        <div style="border-top: 1px solid #d1d5db; padding: 8px; display: flex; background-color: white;">
            <input type="text" id="ai-input" style="flex: 1; border: 1px solid #9ca3af; padding: 8px; outline: none; color: #1f2937; border-radius: 0;" placeholder="Ketik pesan...">
            <button id="ai-send" style="background-color: #047857; color: white; padding: 8px 16px; margin-left: 8px; font-weight: bold; border: none; cursor: pointer; min-height: 44px; border-radius: 0;">
                Kirim
            </button>
        </div>
    </div>

    <!-- Tombol Trigger (Kotak Hijau Tegas) -->
    <button id="ai-trigger" style="background-color: #047857; color: white; font-weight: bold; padding: 12px 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: none; cursor: pointer; min-height: 44px; min-width: 44px; z-index: 10000; border-radius: 0; letter-spacing: 0.05em;">
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
        div.style.cssText = isUser 
            ? 'background-color: #d1fae5; color: #064e3b; padding: 12px; align-self: flex-end; border: 1px solid #6ee7b7; max-width: 85%; border-radius: 0;'
            : 'background-color: #e5e7eb; color: #1f2937; padding: 12px; align-self: flex-start; border: 1px solid #d1d5db; max-width: 85%; border-radius: 0;';
        div.textContent = text;
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
