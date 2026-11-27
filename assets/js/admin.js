/* Admin: mobile sidebar menu */
(function () {
    var toggle  = document.getElementById('adminMenuToggle');
    var sidebar = document.querySelector('.admin-sidebar');
    if (!toggle || !sidebar) return;

    var overlay = document.createElement('div');
    overlay.className = 'admin-sidebar-overlay';
    document.body.appendChild(overlay);

    function setOpen(open) {
        sidebar.classList.toggle('open', open);
        overlay.classList.toggle('show', open);
        document.body.classList.toggle('admin-menu-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    toggle.setAttribute('aria-label', 'Menyja');
    toggle.addEventListener('click', function () {
        setOpen(!sidebar.classList.contains('open'));
    });
    overlay.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
    });
    sidebar.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () { setOpen(false); });
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 1000) setOpen(false);
    });
})();
