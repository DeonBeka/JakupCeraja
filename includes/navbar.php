<?php
$jc_page = basename(parse_url($_SERVER['SCRIPT_NAME'] ?? 'index.php', PHP_URL_PATH));
$jc_active = [
    'index.php'          => 'index.php',
    'about.php'          => 'about.php',
    'books.php'          => 'books.php',
    'book-details.php'   => 'books.php',
    'authors.php'        => 'authors.php',
    'author-details.php' => 'authors.php',
    'gallery.php'        => 'gallery.php',
    'news.php'           => 'news.php',
    'news-details.php'   => 'news.php',
    'contact.php'        => 'contact.php',
][$jc_page] ?? '';
$jc_cart_count = function_exists('jc_cart_count') ? jc_cart_count() : 0;
$jc_toast = '';
if ($jc_page !== 'cart.php' && !empty($_SESSION['cart_flash'])) {
    $jc_toast = $_SESSION['cart_flash'];
    unset($_SESSION['cart_flash']);
}
$jc_links = [
    'index.php'   => 'Ballina',
    'about.php'   => 'Rreth Nesh',
    'books.php'   => 'Librat',
    'authors.php' => 'Autorët',
    'gallery.php' => 'Galeria',
    'news.php'    => 'Lajmet',
    'contact.php' => 'Kontakti',
];
?>
<header class="header">

    <div class="container">

        <a href="index.php" class="logo">
            <img src="assets/images/logo/logo.png" alt="Jakup Ceraja Logo">
        </a>

        <nav class="navbar">

            <ul class="nav-links">
                <?php foreach ($jc_links as $href => $label): ?>
                    <li><a href="<?php echo $href; ?>"<?php echo $jc_active === $href ? ' class="active"' : ''; ?>><?php echo $label; ?></a></li>
                <?php endforeach; ?>
            </ul>

        </nav>

        <div class="nav-right">

            <a href="search.php" class="search-btn" aria-label="Kërko">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>

            <a href="cart.php" class="search-btn cart-btn<?php echo $jc_page === 'cart.php' ? ' is-active' : ''; ?>" aria-label="Shporta">
                <i class="fa-solid fa-bag-shopping"></i>
                <?php if ($jc_cart_count > 0): ?>
                    <span class="cart-count"><?php echo (int) $jc_cart_count; ?></span>
                <?php endif; ?>
            </a>

            <button class="menu-btn" aria-label="Menyja">
                <i class="fa-solid fa-bars"></i>
            </button>

        </div>

    </div>

</header>

<?php if ($jc_toast): ?>
    <div class="cart-toast" role="status">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php echo htmlspecialchars($jc_toast); ?></span>
        <a href="cart.php">Shiko shportën</a>
    </div>
<?php endif; ?>
