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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-bg text-ink antialiased min-h-screen flex flex-col">
    @include('components.navbar-publik')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer-publik')

    @stack('scripts')

<!-- ================= CHATBOT AI LAYANAN PUBLIK ================= -->
<style>
    /* Trigger Button: Fixed di Kanan Bawah */
    #ai-trigger-container {
        position: fixed;
        bottom: 1.25rem;
        right: 1.25rem;
        z-index: 9999;
    }

    #ai-trigger-btn {
        background-color: #047857;
        color: #ffffff;
        font-weight: 600;
        padding: 10px 18px;
        box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.25);
        border: none;
        cursor: pointer;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: transform 0.2s, background-color 0.2s;
    }
    #ai-trigger-btn:hover {
        background-color: #065f46;
        transform: translateY(-2px);
    }

    /* Modal Dasar */
    #ai-modal {
        display: none;
        flex-direction: column;
        background-color: #ffffff;
        z-index: 10000;
        overflow: hidden;
    }

    /* Desktop View: Kotak Kompak di Kanan Bawah */
    @media (min-width: 641px) {
        #ai-modal {
            position: fixed;
            bottom: 4.75rem;
            right: 1.25rem;
            width: 360px;
            height: 460px;
            max-height: calc(100vh - 6rem);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
    }

    /* Mobile View (HP): Bottom Sheet Setengah Layar Bawah */
    @media (max-width: 640px) {
        #ai-modal {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100vw;
            max-width: 100vw;
            height: 52vh;
            max-height: 52vh;
            border-radius: 16px 16px 0 0;
            border-top: 1px solid #d1d5db;
            box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.25);
        }
    }

    .chip-suggestion {
        font-size: 11px;
        padding: 4px 10px;
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        border-radius: 9999px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .chip-suggestion:hover {
        background-color: #dcfce7;
    }
</style>

<div id="ai-trigger-container">
    <button id="ai-trigger-btn" type="button" aria-label="Buka Chat AI Asisten">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        <span>Tanya AI Toko</span>
    </button>
</div>

<div id="ai-modal" role="dialog" aria-modal="true" aria-label="Asisten Virtual Toko Firo">
    <div style="background-color: #065f46; color: #ffffff; padding: 12px 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #34d399; display: inline-block;"></span>
            <span style="font-size: 14px;">Asisten Toko Firo</span>
        </div>
        <button id="ai-close-btn" type="button" style="background: none; border: none; color: #ffffff; font-size: 18px; line-height: 1; cursor: pointer;" aria-label="Tutup Chat">&times;</button>
    </div>

    <div id="ai-history" style="padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background-color: #f9fafb; flex: 1; font-size: 13px; line-height: 1.45;"></div>

    <div id="ai-suggestions" style="padding: 6px 12px; background-color: #ffffff; border-top: 1px solid #f3f4f6; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap;">
        <span class="chip-suggestion" onclick="kirimCepat('Ada kertas HVS A4?')">Kertas A4</span>
        <span class="chip-suggestion" onclick="kirimCepat('Bisa fotokopi?')">Fotokopi</span>
        <span class="chip-suggestion" onclick="kirimCepat('Alamat toko di mana?')">Lokasi Toko</span>
    </div>

    <div style="border-top: 1px solid #e5e7eb; padding: 10px; display: flex; background-color: #ffffff; gap: 8px;">
        <input type="text" id="ai-input" style="flex: 1; border: 1px solid #d1d5db; padding: 8px 12px; outline: none; color: #1f2937; border-radius: 6px; font-size: 13px;" placeholder="Tanya stok atau info toko...">
        <button id="ai-send-btn" type="button" style="background-color: #047857; color: #ffffff; padding: 8px 14px; font-weight: 600; border: none; cursor: pointer; border-radius: 6px; font-size: 13px;">
            Kirim
        </button>
    </div>
</div>

<script>
(function() {
    const trigger = document.getElementById('ai-trigger-btn');
    const modal   = document.getElementById('ai-modal');
    const closeBtn= document.getElementById('ai-close-btn');
    const input   = document.getElementById('ai-input');
    const sendBtn = document.getElementById('ai-send-btn');
    const history = document.getElementById('ai-history');

    let welcomeSent = false;

    function toggleChat() {
        const isHidden = modal.style.display === 'none' || !modal.style.display;
        if (isHidden) {
            modal.style.display = 'flex';
            if (!welcomeSent && history.children.length === 0) {
                appendMsg("Halo! Selamat datang di Toko Firo 👋\nAda yang bisa kami bantu seputar stok ATK, harga produk, fotokopi, atau alamat toko?", false);
                welcomeSent = true;
            }
            setTimeout(() => input.focus(), 150);
        } else {
            modal.style.display = 'none';
        }
    }

    if (trigger) trigger.addEventListener('click', toggleChat);
    if (closeBtn) closeBtn.addEventListener('click', () => modal.style.display = 'none');

    // Rendering Teks Aman Anti-XSS (Menggunakan textContent murni)
    function appendMsg(text, isUser) {
        const div = document.createElement('div');
        div.style.cssText = isUser 
            ? 'background-color: #d1fae5; color: #064e3b; padding: 9px 13px; align-self: flex-end; border: 1px solid #a7f3d0; max-width: 85%; border-radius: 8px; word-break: break-word;'
            : 'background-color: #f3f4f6; color: #1f2937; padding: 9px 13px; align-self: flex-start; border: 1px solid #e5e7eb; max-width: 85%; border-radius: 8px; white-space: pre-line; word-break: break-word;';
        
        div.textContent = text;
        history.appendChild(div);
        history.scrollTop = history.scrollHeight;
    }

    async function submitMessage(pesanTeks) {
        const text = (pesanTeks || input.value).trim();
        if (!text) return;

        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        appendMsg(text, true);
        appendMsg('Sedang memeriksa stok...', false);

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const res = await fetch('/api/chatbot/tanya', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenMeta ? tokenMeta.content : ''
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            history.lastChild.remove();
            appendMsg(data.reply || data.jawaban || 'Maaf, tidak ada balasan.', false);
        } catch (e) {
            history.lastChild.remove();
            appendMsg('Koneksi terputus. Silakan coba lagi.', false);
        } finally {
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    if (sendBtn) sendBtn.addEventListener('click', () => submitMessage());
    if (input) {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') submitMessage();
        });
    }

    window.kirimCepat = function(pertanyaan) {
        submitMessage(pertanyaan);
    };
})();
</script>
<!-- ================= END CHATBOT AI ================= -->
</body>
</html>
