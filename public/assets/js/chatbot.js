(function () {
    'use strict';

    const root = document.querySelector('#unidaChatbot');
    const toggle = document.querySelector('#unidaChatbotToggle');
    const panel = document.querySelector('#unidaChatbotPanel');
    const closeBtn = document.querySelector('#unidaChatbotClose');
    const form = document.querySelector('#unidaChatbotForm');
    const input = document.querySelector('#unidaChatbotInput');
    const messages = document.querySelector('#unidaChatbotMessages');

    if (!root || !toggle || !panel || !form || !input || !messages) return;

    let currentLang = root.getAttribute('data-lang') || window.UNIDA_LANG || 'sw';
    let sending = false;

    const meta = {
        sw: {
            typing: 'Unny anaandika...',
            error: 'Kuna changamoto ya muunganisho. Tafadhali jaribu tena.'
        },
        en: {
            typing: 'Unny is typing...',
            error: 'Connection error. Please try again.'
        }
    };

    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = type === 'user' ? 'user-msg' : 'bot-msg';
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return div;
    }

    function openChat() {
        root.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        setTimeout(function () {
            input.focus();
        }, 80);
    }

    function closeChat() {
        root.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        root.classList.contains('open') ? closeChat() : openChat();
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeChat);
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (sending) return;

        const text = input.value.trim();
        if (!text) return;

        sending = true;
        form.classList.add('is-sending');

        addMessage(text, 'user');
        input.value = '';

        const loading = addMessage((meta[currentLang] || meta.sw).typing, 'bot');

        const controller = new AbortController();
        const timeoutId = setTimeout(function () {
            controller.abort();
        }, 10000);

        try {
            const response = await fetch((window.UNIDA_BASE_URL || '/') + 'api/chatbot.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                signal: controller.signal,
                body: JSON.stringify({
                    message: text,
                    assistant: 'unny',
                    lang: currentLang
                })
            });

            clearTimeout(timeoutId);

            const data = await response.json();
            loading.remove();

            currentLang = data.lang || currentLang;
            addMessage(data.reply || (currentLang === 'sw' ? 'Samahani, sikuweza kujibu sasa.' : 'Sorry, I could not answer right now.'), 'bot');
        } catch (error) {
            loading.remove();
            addMessage((meta[currentLang] || meta.sw).error, 'bot');
        } finally {
            sending = false;
            form.classList.remove('is-sending');
        }
    });
})();
