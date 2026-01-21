// Данные для меню
const menuData = {
    admin: [
        { href: 'dashboard.php', text: 'Admin Dashboard' },
        { href: 'logout.php', text: 'Logout' }
    ],
    user: [
        { href: 'unicorns.php', text: 'Unicorns' },
        { href: 'products.php', text: 'Products' },
        { href: 'cart.php', text: 'Cart (<span class="cart-count">0</span>)' },
        {href: 'favourites.php', text: 'Favourites'},
        { href: 'dashboard.php', text: 'Profile' },
        { href: 'logout.php', text: 'Logout' }
    ],
    guest: [
        { href: 'unicorns.php', text: 'Unicorns' },
        { href: 'products.php', text: 'Products' },
        { href: 'login.php', text: 'Login' },
        { href: 'register.php', text: 'Register' }
    ]
};

document.addEventListener('DOMContentLoaded', function () {
    console.log('Menu loaded');
   
    //  Определяем тип пользователя
    const userData = window.userData || { isLoggedIn: false, role: null };
    let links = menuData.guest;
    
    if (userData.isLoggedIn) {
        links = userData.role === 'admin' ? menuData.admin : menuData.user;
    }
    
    console.log('User data:', userData);

    //  Создаём десктопное меню
    function createDesktopMenu() {
        const container = document.getElementById('desktop-nav-links');
        if (!container) return;
        
        container.innerHTML = '';
        links.forEach(link => {
            const a = document.createElement('a');
            a.href = link.href;
            a.className = 'nav-link';
            a.style.color = 'white';
            a.style.textDecoration = 'none';
            a.innerHTML = link.text;
            container.appendChild(a);
        });
    }

    //  Создаём мобильное меню
    function createMobileMenu() {
        const container = document.getElementById('mobile-nav-links');
        if (!container) return;
        
        container.innerHTML = '';
        links.forEach(link => {
            const a = document.createElement('a');
            a.href = link.href;
            a.className = 'nav-link';
            a.style.color = '#2c3e50'; //  Исправлен цвет
            a.style.textDecoration = 'none';
            a.style.padding = '15px 20px'; //  Убран пробел
            a.style.display = 'block';
            a.style.borderBottom = '1px solid #eee';
            a.innerHTML = link.text;
            container.appendChild(a);
        });
    }

    //  Инициализация меню
    createDesktopMenu();
    createMobileMenu();
    
    //  Обновляем корзину
    updateCartCount();
});

function toggleMobileMenu() {
    const menu = document.querySelector('.mobile-navbar-links');
    if (menu) menu.classList.toggle('active');
}

//  Закрываем меню при клике вне его
document.addEventListener('click', function (event) {
    const menu = document.querySelector('.mobile-navbar-links');
    const toggleBtn = document.querySelector('.mobile-menu-toggle');

    if (menu && toggleBtn && !menu.contains(event.target) && !toggleBtn.contains(event.target)) {
        menu.classList.remove('active');
    }
});

//  Обновление счётчика корзины
function updateCartCount() {
    //  Исправлено: getTime() вместо getTime
    fetch('/get_cart_count.php?' + new Date().getTime(), {
        cache: 'no-cache'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCountEls = document.querySelectorAll('.cart-count');
            cartCountEls.forEach(el => {
                el.textContent = data.count;
            });
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
    });
}