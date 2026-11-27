<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';

$allowed_statuses = [
    'new'       => 'E re',
    'confirmed' => 'Konfirmuar',
    'shipped'   => 'Dërguar',
    'completed' => 'Përfunduar',
    'cancelled' => 'Anuluar',
];

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// ==========================================
// ACTIONS (change status / delete)
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $order_id = (int) ($_POST['order_id'] ?? 0);
    $back = 'orders.php' . (!empty($_POST['back']) ? '?' . preg_replace('/[^a-zA-Z0-9=&_%-]/', '', $_POST['back']) : '');

    if (isset($_POST['update_status']) && isset($allowed_statuses[$_POST['status'] ?? ''])) {
        $st = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $st->bind_param('si', $_POST['status'], $order_id);
        $st->execute();
        $_SESSION['orders_flash'] = 'Statusi u përditësua.';
    }

    if (isset($_POST['delete_order'])) {
        $st = $conn->prepare('DELETE FROM order_items WHERE order_id = ?');
        $st->bind_param('i', $order_id);
        $st->execute();
        $st = $conn->prepare('DELETE FROM orders WHERE id = ?');
        $st->bind_param('i', $order_id);
        $st->execute();
        $_SESSION['orders_flash'] = 'Porosia u fshi.';
        $back = 'orders.php';
    }

    header('Location: ' . $back);
    exit;
}

$flash = $_SESSION['orders_flash'] ?? '';
unset($_SESSION['orders_flash']);

// ==========================================
// STATS
// ==========================================

$stats = ['all' => 0, 'new' => 0, 'confirmed' => 0, 'shipped' => 0, 'completed' => 0, 'cancelled' => 0];
$revenue = 0;
$res = $conn->query('SELECT status, COUNT(*) AS c, SUM(total_price) AS t FROM orders GROUP BY status');
while ($res && $r = $res->fetch_assoc()) {
    $stats[$r['status']] = (int) $r['c'];
    $stats['all'] += (int) $r['c'];
    if ($r['status'] !== 'cancelled') {
        $revenue += (float) $r['t'];
    }
}

// ==========================================
// LIST (filter + search)
// ==========================================

$filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$where  = [];
$types  = '';
$params = [];

if (isset($allowed_statuses[$filter])) {
    $where[] = 'o.status = ?';
    $types .= 's';
    $params[] = $filter;
}
if ($search !== '') {
    $where[] = '(o.fullname LIKE ? OR o.email LIKE ? OR o.phone LIKE ? OR o.order_code LIKE ? OR o.city LIKE ?)';
    $like = '%' . $search . '%';
    $types .= 'sssss';
    array_push($params, $like, $like, $like, $like, $like);
}

$sql = 'SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS items_count
        FROM orders o' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . '
        ORDER BY o.created_at DESC, o.id DESC LIMIT 200';
$st = $conn->prepare($sql);
if ($params) {
    $st->bind_param($types, ...$params);
}
$st->execute();
$orders = $st->get_result()->fetch_all(MYSQLI_ASSOC);

// items for the listed orders
$items_by_order = [];
if ($orders) {
    $ids = implode(',', array_map('intval', array_column($orders, 'id')));
    $res = $conn->query("SELECT * FROM order_items WHERE order_id IN ($ids) ORDER BY id");
    while ($res && $r = $res->fetch_assoc()) {
        $items_by_order[$r['order_id']][] = $r;
    }
}

$new_orders = $stats["new"];
$h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$back_qs = http_build_query(array_filter(['status' => $filter, 'search' => $search]));

?>

<!DOCTYPE html>

<html lang="sq">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Porositë | Admin
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/animations.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

<link rel="stylesheet" href="../assets/css/admin-mobile.css">
</head>


<body>


<div class="admin-layout">


    <!-- ======================================
         SIDEBAR
    ======================================= -->

    <aside class="admin-sidebar">


        <div class="admin-logo">

            <div class="admin-logo-icon">

                <i class="fa-solid fa-book-open"></i>

            </div>

            <div>

                <strong>
                    Jakup Ceraja
                </strong>

                <span>
                    Admin Panel
                </span>

            </div>

        </div>


        <nav class="admin-nav">


            <a href="dashboard.php">

                <i class="fa-solid fa-chart-line"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="books.php">

                <i class="fa-solid fa-book"></i>

                <span>
                    Libra
                </span>

            </a>


            <a href="authors.php">

                <i class="fa-solid fa-pen-nib"></i>

                <span>
                    Autorë
                </span>

            </a>


            <a href="categories.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Kategori
                </span>

            </a>


            <a href="news.php">

                <i class="fa-solid fa-newspaper"></i>

                <span>
                    Lajme
                </span>

            </a>


            <a href="gallery.php">

                <i class="fa-solid fa-images"></i>

                <span>
                    Galeri
                </span>

            </a>


            <a href="messages.php">

                <i class="fa-solid fa-envelope"></i>

                <span>
                    Mesazhe
                </span>

            </a>


            <a
                href="orders.php"
                class="active"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Porosi
                </span>

                <?php if ($new_orders > 0): ?>

                    <span class="nav-badge">
                        <?php echo $new_orders; ?>
                    </span>

                <?php endif; ?>

            </a>


            <a href="settings.php">

                <i class="fa-solid fa-gear"></i>

                <span>
                    Cilësime
                </span>

            </a>


        </nav>


        <div class="admin-sidebar-bottom">


            <a
                href="../index.php"
                target="_blank"
            >

                <i class="fa-solid fa-globe"></i>

                Shiko faqen

            </a>


            <a href="logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                Dil

            </a>


        </div>


    </aside>


    <!-- ======================================
         MAIN
    ======================================= -->

    <main class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">


            <button
                type="button"
                class="admin-menu-toggle"
                id="adminMenuToggle"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div class="admin-topbar-title">

                <span>
                    MENAXHIMI
                </span>

                <h1>
                    Porositë
                </h1>

            </div>


            <div class="admin-topbar-actions">


                <button
                    type="button"
                    class="admin-icon-btn"
                    id="darkModeToggle"
                    title="Dark mode"
                >

                    <i class="fa-solid fa-moon"></i>

                </button>


                <a
                    href="../index.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Faqja

                </a>


            </div>


        </header>


        <!-- CONTENT -->

        <div class="admin-content">

            <?php if ($flash): ?>
                <div class="orders-flash"><i class="fa-solid fa-circle-check"></i> <?php echo $h($flash); ?></div>
            <?php endif; ?>

            <div class="orders-kpis">
                <div class="orders-kpi">
                    <span>Të gjitha</span>
                    <strong><?php echo $stats['all']; ?></strong>
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="orders-kpi kpi-new">
                    <span>Të reja</span>
                    <strong><?php echo $stats['new']; ?></strong>
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="orders-kpi">
                    <span>Në proces</span>
                    <strong><?php echo $stats['confirmed'] + $stats['shipped']; ?></strong>
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div class="orders-kpi">
                    <span>Të ardhura</span>
                    <strong><?php echo jc_money($revenue); ?></strong>
                    <i class="fa-solid fa-euro-sign"></i>
                </div>
            </div>

            <section class="admin-panel-card orders-panel">

                <div class="orders-toolbar">
                    <div class="orders-tabs">
                        <a href="orders.php<?php echo $search !== '' ? '?search=' . urlencode($search) : ''; ?>" class="<?php echo $filter === '' ? 'active' : ''; ?>">Të gjitha <em><?php echo $stats['all']; ?></em></a>
                        <?php foreach ($allowed_statuses as $key => $label): ?>
                            <a href="?<?php echo $h(http_build_query(array_filter(['status' => $key, 'search' => $search]))); ?>" class="<?php echo $filter === $key ? 'active' : ''; ?>"><?php echo $label; ?> <em><?php echo $stats[$key]; ?></em></a>
                        <?php endforeach; ?>
                    </div>
                    <form class="orders-search" method="get">
                        <?php if ($filter): ?><input type="hidden" name="status" value="<?php echo $h($filter); ?>"><?php endif; ?>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" name="search" value="<?php echo $h($search); ?>" placeholder="Kërko emër, telefon, kod…">
                    </form>
                </div>

                <?php if (!$orders): ?>
                    <div class="orders-empty">
                        <i class="fa-solid fa-inbox"></i>
                        <p><?php echo ($filter || $search) ? 'Asnjë porosi nuk përputhet me kërkimin.' : 'Ende nuk ka porosi. Kur dikush blen libra, porositë shfaqen këtu.'; ?></p>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $o): $items = $items_by_order[$o['id']] ?? []; ?>
                            <details class="order-card<?php echo $o['status'] === 'new' ? ' is-new' : ''; ?>">
                                <summary>
                                    <div class="order-main">
                                        <div class="order-code"><?php echo $h($o['order_code']); ?></div>
                                        <div class="order-customer">
                                            <strong><?php echo $h($o['fullname']); ?></strong>
                                            <span><?php echo $h($o['city']); ?> · <?php echo date('d.m.Y H:i', strtotime($o['created_at'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="order-meta">
                                        <span class="order-items-count"><i class="fa-solid fa-book"></i> <?php echo (int) $o['items_count']; ?></span>
                                        <span class="order-status s-<?php echo $h($o['status']); ?>"><?php echo $h($allowed_statuses[$o['status']] ?? $o['status']); ?></span>
                                        <strong class="order-total"><?php echo jc_money($o['total_price']); ?></strong>
                                        <i class="fa-solid fa-chevron-down order-chevron"></i>
                                    </div>
                                </summary>

                                <div class="order-body">
                                    <div class="order-books">
                                        <?php foreach ($items as $it): ?>
                                            <div class="order-book">
                                                <div class="order-book-cover">
                                                    <?php if ($it['book_cover']): ?><img src="../<?php echo $h($it['book_cover']); ?>" alt=""><?php else: ?><i class="fa-solid fa-book"></i><?php endif; ?>
                                                </div>
                                                <div class="order-book-info">
                                                    <strong><?php echo $h($it['book_title']); ?></strong>
                                                    <span><?php echo (int) $it['quantity']; ?> × <?php echo jc_money($it['unit_price']); ?></span>
                                                </div>
                                                <div class="order-book-total"><?php echo jc_money($it['quantity'] * $it['unit_price']); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="order-sums">
                                            <div><span>Librat</span><span><?php echo jc_money($o['subtotal']); ?></span></div>
                                            <div><span>Dërgesa</span><span><?php echo (float) $o['shipping_price'] > 0 ? jc_money($o['shipping_price']) : 'Falas'; ?></span></div>
                                            <div class="order-sums-total"><span>Totali</span><span><?php echo jc_money($o['total_price']); ?></span></div>
                                        </div>
                                    </div>

                                    <div class="order-side">
                                        <div class="order-contact">
                                            <h4>Klienti</h4>
                                            <a href="tel:<?php echo $h(preg_replace('/[^0-9+]/', '', $o['phone'])); ?>"><i class="fa-solid fa-phone"></i> <?php echo $h($o['phone']); ?></a>
                                            <a href="mailto:<?php echo $h($o['email']); ?>"><i class="fa-solid fa-envelope"></i> <?php echo $h($o['email']); ?></a>
                                            <p><i class="fa-solid fa-location-dot"></i> <?php echo $h($o['address']); ?>, <?php echo $h($o['city']); ?>, <?php echo $h($o['country']); ?></p>
                                            <?php if (!empty($o['note'])): ?>
                                                <p class="order-note"><i class="fa-solid fa-message"></i> <?php echo nl2br($h($o['note'])); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <form method="post" class="order-status-form">
                                            <input type="hidden" name="csrf" value="<?php echo $h($_SESSION['csrf']); ?>">
                                            <input type="hidden" name="order_id" value="<?php echo (int) $o['id']; ?>">
                                            <input type="hidden" name="back" value="<?php echo $h($back_qs); ?>">
                                            <label for="st-<?php echo (int) $o['id']; ?>">Ndrysho statusin</label>
                                            <div class="order-status-row">
                                                <select id="st-<?php echo (int) $o['id']; ?>" name="status">
                                                    <?php foreach ($allowed_statuses as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>"<?php echo $o['status'] === $key ? ' selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" name="update_status" value="1" class="admin-primary-button">Ruaj</button>
                                            </div>
                                        </form>

                                        <form method="post" onsubmit="return confirm('A jeni i sigurt që doni ta fshini këtë porosi?');">
                                            <input type="hidden" name="csrf" value="<?php echo $h($_SESSION['csrf']); ?>">
                                            <input type="hidden" name="order_id" value="<?php echo (int) $o['id']; ?>">
                                            <button type="submit" name="delete_order" value="1" class="order-delete"><i class="fa-solid fa-trash"></i> Fshi porosinë</button>
                                        </form>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </section>

        </div>

    </main>

</div>

<script src="../assets/js/admin.js"></script>

</body>

</html>
