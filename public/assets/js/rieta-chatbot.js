(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {
        const root = document.querySelector('#rietaChatbot');
        if (!root) return;

        const toggle = document.querySelector('#rietaToggle');
        const closeBtn = document.querySelector('#rietaClose');
        const body = document.querySelector('#rietaBody');
        const form = document.querySelector('#rietaForm');
        const input = document.querySelector('#rietaInput');
        const supportForm = document.querySelector('#rietaSupportForm');

        if (!toggle || !body || !form || !input || !supportForm) return;

        let lang = root.dataset.lang || window.UNIDA_LANG || 'en';
        let sending = false;
        let lastConcern = '';

        const labels = {
            en: {
                typing: 'Unny is typing...',
                error: 'Connection error. Please try again.',
                fill: 'Please fill your details below so I can forward this to a human agent.'
            },
            sw: {
                typing: 'Rieta anaandika...',
                error: 'Kuna changamoto ya muunganisho. Tafadhali jaribu tena.',
                fill: 'Tafadhali jaza taarifa zako hapa chini ili nitume kwa human agent.'
            }
        };

        function addMessage(text, type) {
            const div = document.createElement('div');
            div.className = 'rieta-msg ' + (type === 'user' ? 'user' : 'bot');
            div.textContent = text;
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
            return div;
        }

        function showSupportForm(concern) {
            if (concern) {
                lastConcern = concern;
                const textarea = supportForm.querySelector('textarea[name="concern"]');
                if (textarea && !textarea.value.trim()) textarea.value = concern;
            }

            supportForm.classList.add('show');
            body.scrollTop = body.scrollHeight;
        }

        function openBot() {
            root.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
            setTimeout(function () {
                input.focus();
            }, 80);
        }

        function closeBot() {
            root.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (root.classList.contains('open')) {
                closeBot();
            } else {
                openBot();
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function (event) {
                event.preventDefault();
                closeBot();
            });
        }

        async function callBot(payload) {
            const controller = new AbortController();
            const timeout = setTimeout(function () {
                controller.abort();
            }, 12000);

            try {
                const response = await fetch((window.UNIDA_BASE_URL || '/') + 'api/rieta-chatbot.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    signal: controller.signal,
                    body: JSON.stringify(payload)
                });

                clearTimeout(timeout);

                if (!response.ok) {
                    return {success: false, reply: (labels[lang] || labels.en).error};
                }

                return await response.json();
            } catch (error) {
                clearTimeout(timeout);
                return {success: false, reply: (labels[lang] || labels.en).error};
            }
        }

        document.querySelectorAll('[data-topic]').forEach(function (button) {
            button.addEventListener('click', async function () {
                if (sending) return;

                const topic = button.dataset.topic;
                const label = button.textContent.trim();

                addMessage(label, 'user');

                if (topic === 'human_agent') {
                    addMessage((labels[lang] || labels.en).fill, 'bot');
                    showSupportForm('');
                    return;
                }

                sending = true;
                const loading = addMessage((labels[lang] || labels.en).typing, 'bot');

                const data = await callBot({
                    action: 'message',
                    topic: topic,
                    message: label,
                    lang: lang
                });

                loading.remove();
                lang = data.lang || lang;
                addMessage(data.reply || (labels[lang] || labels.en).error, 'bot');

                if (data.needs_human_details) {
                    showSupportForm(label);
                }

                sending = false;
            });
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (sending) return;

            const text = input.value.trim();
            if (!text) return;

            input.value = '';
            lastConcern = text;
            addMessage(text, 'user');

            sending = true;
            const loading = addMessage((labels[lang] || labels.en).typing, 'bot');

            const data = await callBot({
                action: 'message',
                message: text,
                lang: lang
            });

            loading.remove();
            lang = data.lang || lang;
            addMessage(data.reply || (labels[lang] || labels.en).error, 'bot');

            if (data.needs_human_details) {
                showSupportForm(text);
            }

            sending = false;
        });

        supportForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (sending) return;

            const formData = new FormData(supportForm);
            const payload = {
                action: 'support_request',
                lang: lang,
                full_name: formData.get('full_name') || '',
                email: formData.get('email') || '',
                phone: formData.get('phone') || '',
                concern: formData.get('concern') || lastConcern || ''
            };

            sending = true;
            const loading = addMessage((labels[lang] || labels.en).typing, 'bot');

            const data = await callBot(payload);

            loading.remove();
            lang = data.lang || lang;
            addMessage(data.reply || (labels[lang] || labels.en).error, 'bot');

            if (data.success) {
                supportForm.reset();
                supportForm.classList.remove('show');
            }

            sending = false;
        });
    });
})();