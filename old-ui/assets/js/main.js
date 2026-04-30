(function () {
    'use strict';

    const menuToggle = document.querySelector('.menu-toggle');
    const mobileNav = document.querySelector('#mobileNav');
    const header = document.querySelector('#siteHeader');

    function closeMobileMenu() {
        if (!menuToggle || !mobileNav) return;

        mobileNav.classList.remove('show');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        document.body.classList.remove('menu-open');
    }

    function openMobileMenu() {
        if (!menuToggle || !mobileNav) return;

        mobileNav.classList.add('show');
        menuToggle.setAttribute('aria-expanded', 'true');
        menuToggle.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        document.body.classList.add('menu-open');
    }

    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function () {
            mobileNav.classList.contains('show') ? closeMobileMenu() : openMobileMenu();
        });

        mobileNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMobileMenu);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMobileMenu();
        });
    }

    function handleHeaderScroll() {
        if (!header) return;
        header.classList.toggle('is-scrolled', window.scrollY > 20);
    }

    handleHeaderScroll();
    window.addEventListener('scroll', handleHeaderScroll, { passive: true });

    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            const targetId = link.getAttribute('href');
            if (!targetId || targetId === '#') return;

            const target = document.querySelector(targetId);
            if (!target) return;

            event.preventDefault();
            closeMobileMenu();

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetSelector = button.getAttribute('data-password-toggle');
            const input = document.querySelector(targetSelector);

            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            button.innerHTML = isPassword
                ? '<i class="fa-solid fa-eye-slash"></i>'
                : '<i class="fa-solid fa-eye"></i>';
        });
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

            if (!submitButton) return;

            if (submitButton.dataset.loading === 'true') return;

            submitButton.dataset.loading = 'true';

            if (submitButton.tagName.toLowerCase() === 'button') {
                submitButton.dataset.originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            }

            submitButton.disabled = true;
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetSelector = button.getAttribute('data-dismiss');
            const target = document.querySelector(targetSelector);

            if (target) target.remove();
        });
    });
})();