const menuToggle = document.querySelector('.menu-toggle');
const mobileNav = document.querySelector('#mobileNav');

if (menuToggle && mobileNav) {
    menuToggle.addEventListener('click', () => {
        const isOpen = mobileNav.classList.toggle('show');
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuToggle.innerHTML = isOpen
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';
    });
}

const header = document.querySelector('#siteHeader');

window.addEventListener('scroll', () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 20);
});

document.querySelectorAll('form[data-demo-form="true"]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        alert('Demo form submitted. Connect this form to your backend or email handler.');
    });
});
