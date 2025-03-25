function goToPage(pageUrl) {
    window.location.href = pageUrl;
}

const hamburger = document.querySelector('.hamburger');
const mobile_menu = document.querySelector('.nav-list ul');
const menu_items = document.querySelectorAll('.nav-list ul li a');
const header = document.querySelector('.header');

if (hamburger && mobile_menu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobile_menu.classList.toggle('active');
    });

    menu_items.forEach((item) => {
        item.addEventListener('click', () => {
            hamburger.classList.remove('active');
            mobile_menu.classList.remove('active');
        });
    });
}

document.addEventListener('scroll', () => {
    if (header) {
        var scroll_position = window.scrollY;
        header.style.backgroundColor = scroll_position > 250 ? '#29323c' : 'transparent';
    }
    
});
