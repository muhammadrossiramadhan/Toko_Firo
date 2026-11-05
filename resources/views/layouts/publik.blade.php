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

    {{-- Floating AI Chatbot Button & Modal (Standar K12: 0 SVG, 0 Emoji, rounded-none) --}}
    <button id="btn-toggle-chatbot" type="button" aria-label="Buka Chatbot Asisten AI" class="fixed bottom-6 right-6 z-40 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-4 py-3 border border-emerald-900 shadow-lg min-h-[44px] min-w-[44px] flex items-center justify-center rounded-none text-sm tracking-wide">
        Tanya Asisten (AI)
    </button>

    <div id="modal-chatbot" class="fixed bottom-20 right-6 z-50 w-80 sm:w-96 bg-white border border-slate-300 shadow-2xl flex flex-col rounded-none hidden">
        {{-- Header Chat --}}
        <div class="bg-emerald-800 text-white px-4 py-2 flex items-center justify-between border-b border-emerald-900">
            <div class="flex items-center space-x-2">
                <span class="inline-block w-2.5 h-2.5 bg-emerald-400 rounded-none"></span>
                <span class="font-bold text-sm tracking-wide">Asisten Toko Firo</span>
            </div>
            <button id="btn-close-chatbot" type="button" class="text-white hover:text-red-300 font-bold min-h-[44px] min-w-[44px] flex items-center justify-center text-sm rounded-none focus:outline-none" aria-label="Tutup Chat">[X]</button>
        </div>

        {{-- Area Riwayat Pesan --}}
        <div id="chat-messages" class="h-80 overflow-y-auto p-4 space-y-3 bg-slate-50 text-sm">
            <div class="flex flex-col items-start">
                <div class="bg-white border border-slate-200 p-2.5 max-w-[85%] text-slate-800 shadow-sm rounded-none">
                    Halo! Saya Asisten Virtual Toko Firo. Ada yang bisa saya bantu terkait produk atau layanan kami?
                </div>
                <span class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">Asisten</span>
            </div>
        </div>

        {{-- Status Sedang Mengetik --}}
        <div id="chat-typing" class="px-4 py-1 text-xs text-slate-500 italic bg-slate-50 hidden border-t border-slate-100">
            Asisten sedang mengetik...
        </div>

        {{-- Form Input Chat --}}
        <div class="p-3 bg-white border-t border-slate-200">
            <form id="form-chatbot" class="flex gap-2" onsubmit="return false;">
                <input type="text" id="chat-input" placeholder="Tanyakan stok, harga, dll..." maxlength="500" class="border border-slate-300 px-3 py-2 text-sm flex-1 rounded-none focus:outline-none focus:border-emerald-700">
                <button id="btn-kirim-chat" type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 text-sm font-semibold min-h-[44px] min-w-[44px] rounded-none">Kirim</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('btn-toggle-chatbot');
        const closeBtn = document.getElementById('btn-close-chatbot');
        const modal = document.getElementById('modal-chatbot');
        const chatForm = document.getElementById('form-chatbot');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const typingIndicator = document.getElementById('chat-typing');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function toggleChat(show) {
            if (show === undefined) {
                modal.classList.toggle('hidden');
            } else if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }

            if (!modal.classList.contains('hidden')) {
                chatInput.focus();
                scrollToBottom();
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                toggleChat();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                toggleChat(false);
            });
        }

        function scrollToBottom() {
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        function appendMessage(sender, text) {
            const wrapper = document.createElement('div');
            wrapper.className = sender === 'user' ? 'flex flex-col items-end' : 'flex flex-col items-start';

            const bubble = document.createElement('div');
            if (sender === 'user') {
                bubble.className = 'bg-emerald-700 text-white p-2.5 max-w-[85%] shadow-sm rounded-none text-sm';
            } else {
                bubble.className = 'bg-white border border-slate-200 p-2.5 max-w-[85%] text-slate-800 shadow-sm rounded-none text-sm';
            }
            bubble.textContent = text;

            const label = document.createElement('span');
            label.className = 'text-[10px] text-slate-400 mt-1 uppercase font-semibold';
            label.textContent = sender === 'user' ? 'Anda' : 'Asisten';

            wrapper.appendChild(bubble);
            wrapper.appendChild(label);
            chatMessages.appendChild(wrapper);
            scrollToBottom();
        }

        async function sendMessage() {
            const text = (chatInput.value || '').trim();
            if (!text) return;

            appendMessage('user', text);
            chatInput.value = '';
            chatInput.disabled = true;

            if (typingIndicator) {
                typingIndicator.classList.remove('hidden');
                scrollToBottom();
            }

            try {
                const response = await fetch('/api/chatbot/tanya', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ pesan: text })
                });

                const data = await response.json();

                if (response.ok && data.jawaban) {
                    appendMessage('bot', data.jawaban);
                } else if (response.status === 429) {
                    appendMessage('bot', 'Terlalu banyak pertanyaan dalam waktu singkat. Mohon tunggu beberapa saat sebelum bertanya kembali.');
                } else if (data.message) {
                    appendMessage('bot', data.message);
                } else {
                    appendMessage('bot', 'Maaf, terjadi kesalahan saat memproses pertanyaan Anda.');
                }
            } catch (err) {
                appendMessage('bot', 'Maaf, terjadi gangguan jaringan. Silakan coba lagi.');
            } finally {
                if (typingIndicator) {
                    typingIndicator.classList.add('hidden');
                }
                chatInput.disabled = false;
                chatInput.focus();
                scrollToBottom();
            }
        }

        if (chatForm) {
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                sendMessage();
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
