<?php


require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// UPDATE ORDER STATUS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    $order_id = isset($_POST['order_id'])
        ? (int) $_POST['order_id']
        : 0;

    $status = isset($_POST['status'])
        ? trim($_POST['status'])
        : '';

    $allowed_statuses = [
        'new',
        'confirmed',
        'shipped',
        'completed',
        'cancelled'
    ];

    if (
        $order_id > 0 &&
        in_array($status, $allowed_statuses, true)
    ) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE orders SET status = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status,
            $order_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: orders.php");
    exit;
}


// ==========================================
// DELETE ORDER
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {

    $order_id = isset($_POST['order_id'])
        ? (int) $_POST['order_id']
        : 0;

    if ($order_id > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM orders WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $order_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: orders.php");
    exit;
}


// ==========================================
// FILTER
// ==========================================

$filter_status = isset($_GET['status'])
    ? trim($_GET['status'])
    : '';

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';


// ==========================================
// BUILD QUERY
// ==========================================

$sql = "
    SELECT
        orders.*,
        books.title AS book_title,
        books.cover AS book_cover

    FROM orders

    LEFT JOIN books
        ON orders.book_id = books.id

    WHERE 1=1
";

$params = [];
$types = "";


// STATUS FILTER

if ($filter_status !== '') {

    $sql .= " AND orders.status = ?";

    $params[] = $filter_status;
    $types .= "s";
}


// SEARCH

if ($search !== '') {

    $sql .= "
        AND (
            orders.fullname LIKE ?
            OR orders.email LIKE ?
            OR orders.phone LIKE ?
            OR orders.city LIKE ?
            OR books.title LIKE ?
        )
    ";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "sssss";
}


$sql .= "
    ORDER BY orders.created_at DESC
";


// ==========================================
// PREPARE QUERY
// ==========================================

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// ==========================================
// ORDER COUNTS
// ==========================================

$count_query = mysqli_query(
    $conn,
    "
    SELECT
        COUNT(*) AS total,

        SUM(status = 'new') AS new_orders,

        SUM(status = 'confirmed') AS confirmed_orders,

        SUM(status = 'shipped') AS shipped_orders,

        SUM(status = 'completed') AS completed_orders,

        SUM(status = 'cancelled') AS cancelled_orders

    FROM orders
"
);

$count_data = mysqli_fetch_assoc($count_query);

$total_orders = (int)($count_data['total'] ?? 0);
$new_orders = (int)($count_data['new_orders'] ?? 0);
$confirmed_orders = (int)($count_data['confirmed_orders'] ?? 0);
$shipped_orders = (int)($count_data['shipped_orders'] ?? 0);
$completed_orders = (int)($count_data['completed_orders'] ?? 0);
$cancelled_orders = (int)($count_data['cancelled_orders'] ?? 0);


// ==========================================
// STATUS NAMES
// ==========================================

$status_names = [

    'new' => 'E re',

    'confirmed' => 'E konfirmuar',

    'shipped' => 'Dërguar',

    'completed' => 'Përfunduar',

    'cancelled' => 'Anuluar'

];

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


            <!-- =================================
                 PAGE HEADER
            ================================== -->

            <div class="admin-page-heading">

                <div>

                    <span>
                        POROSITË E KLIENTËVE
                    </span>

                    <h2>
                        Menaxho porositë
                    </h2>

                    <p>
                        Shiko të gjitha porositë dhe
                        përditëso statusin e tyre.
                    </p>

                </div>

            </div>


            <!-- =================================
                 ORDER STATS
            ================================== -->

            <div class="admin-order-stats">


                <div class="admin-order-stat">

                    <div>

                        <span>
                            Të gjitha
                        </span>

                        <strong>
                            <?php echo $total_orders; ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-cart-shopping"></i>

                </div>


                <div class="admin-order-stat">

                    <div>

                        <span>
                            Të reja
                        </span>

                        <strong>
                            <?php echo $new_orders; ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-bell"></i>

                </div>


                <div class="admin-order-stat">

                    <div>

                        <span>
                            Të dërguara
                        </span>

                        <strong>
                            <?php echo $shipped_orders; ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-truck"></i>

                </div>


                <div class="admin-order-stat">

                    <div>

                        <span>
                            Përfunduara
                        </span>

                        <strong>
                            <?php echo $completed_orders; ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-check"></i>

                </div>


            </div>


            <!-- =================================
                 FILTERS
            ================================== -->

            <div class="admin-filter-card">


                <form
                    method="GET"
                    action="orders.php"
                    class="admin-filter-form"
                >


                    <div class="admin-search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            name="search"
                            placeholder="Kërko klientin, librin, telefonin..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <select
                        name="status"
                        class="admin-filter-select"
                    >

                        <option value="">
                            Të gjitha statuset
                        </option>

                        <option
                            value="new"
                            <?php echo $filter_status === 'new' ? 'selected' : ''; ?>
                        >
                            E re
                        </option>

                        <option
                            value="confirmed"
                            <?php echo $filter_status === 'confirmed' ? 'selected' : ''; ?>
                        >
                            E konfirmuar
                        </option>

                        <option
                            value="shipped"
                            <?php echo $filter_status === 'shipped' ? 'selected' : ''; ?>
                        >
                            Dërguar
                        </option>

                        <option
                            value="completed"
                            <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>
                        >
                            Përfunduar
                        </option>

                        <option
                            value="cancelled"
                            <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>
                        >
                            Anuluar
                        </option>

                    </select>


                    <button
                        type="submit"
                        class="admin-filter-button"
                    >

                        <i class="fa-solid fa-filter"></i>

                        Filtro

                    </button>


                    <?php if ($search !== '' || $filter_status !== ''): ?>

                        <a
                            href="orders.php"
                            class="admin-clear-filter"
                        >

                            Pastro

                        </a>

                    <?php endif; ?>


                </form>


            </div>


            <!-- =================================
                 ORDERS
            ================================== -->

            <div class="admin-orders-list">


                <?php if ($result && mysqli_num_rows($result) > 0): ?>


                    <?php while ($order = mysqli_fetch_assoc($result)): ?>


                        <article class="admin-order-card">


                            <!-- ORDER TOP -->

                            <div class="admin-order-card-top">


                                <div class="admin-order-number">

                                    <span>
                                        POROSIA
                                    </span>

                                    <strong>
                                        #<?php echo (int)$order['id']; ?>
                                    </strong>

                                </div>


                                <div class="admin-order-date">

                                    <i class="fa-regular fa-calendar"></i>

                                    <?php

                                    echo date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            $order['created_at']
                                        )
                                    );

                                    ?>

                                </div>


                            </div>


                            <!-- ORDER BODY -->

                            <div class="admin-order-card-body">


                                <!-- CUSTOMER -->

                                <div class="admin-order-section">


                                    <div class="admin-order-section-title">

                                        <i class="fa-solid fa-user"></i>

                                        Klienti

                                    </div>


                                    <div class="admin-customer-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $order['fullname']
                                        );

                                        ?>

                                    </div>


                                    <div class="admin-customer-details">

                                        <span>

                                            <i class="fa-solid fa-phone"></i>

                                            <?php

                                            echo htmlspecialchars(
                                                $order['phone']
                                            );

                                            ?>

                                        </span>


                                        <?php if (!empty($order['email'])): ?>

                                            <span>

                                                <i class="fa-solid fa-envelope"></i>

                                                <?php

                                                echo htmlspecialchars(
                                                    $order['email']
                                                );

                                                ?>

                                            </span>

                                        <?php endif; ?>


                                    </div>


                                </div>


                                <!-- ADDRESS -->

                                <div class="admin-order-section">


                                    <div class="admin-order-section-title">

                                        <i class="fa-solid fa-location-dot"></i>

                                        Adresa e dërgesës

                                    </div>


                                    <div class="admin-address">

                                        <?php

                                        echo htmlspecialchars(
                                            $order['address']
                                        );

                                        ?>

                                    </div>


                                    <div class="admin-address-location">

                                        <?php

                                        echo htmlspecialchars(
                                            $order['city']
                                        );

                                        ?>,

                                        <?php

                                        echo htmlspecialchars(
                                            $order['country']
                                        );

                                        ?>

                                    </div>


                                </div>


                                <!-- BOOK -->

                                <div class="admin-order-section">


                                    <div class="admin-order-section-title">

                                        <i class="fa-solid fa-book"></i>

                                        Libri

                                    </div>


                                    <div class="admin-order-book">


                                        <div class="admin-order-book-cover">


                                            <?php if (!empty($order['book_cover'])): ?>

                                                <img
                                                    src="../assets/uploads/<?php echo htmlspecialchars($order['book_cover']); ?>"
                                                    alt=""
                                                >

                                            <?php else: ?>

                                                <i class="fa-solid fa-book"></i>

                                            <?php endif; ?>


                                        </div>


                                        <div>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $order['book_title']
                                                    ?? 'Libër i fshirë'
                                                );

                                                ?>

                                            </strong>

                                            <span>

                                                <?php

                                                echo number_format(
                                                    (float)$order['book_price'],
                                                    2
                                                );

                                                ?> €

                                            </span>

                                        </div>


                                    </div>


                                </div>


                                <!-- PRICE -->

                                <div class="admin-order-section admin-order-price-section">


                                    <div class="admin-price-row">

                                        <span>
                                            Libri
                                        </span>

                                        <strong>

                                            <?php

                                            echo number_format(
                                                (float)$order['book_price'],
                                                2
                                            );

                                            ?> €

                                        </strong>

                                    </div>


                                    <div class="admin-price-row">

                                        <span>
                                            Posta
                                        </span>

                                        <strong>

                                            <?php

                                            echo number_format(
                                                (float)$order['shipping_price'],
                                                2
                                            );

                                            ?> €

                                        </strong>

                                    </div>


                                    <div class="admin-price-total">

                                        <span>
                                            TOTALI
                                        </span>

                                        <strong>

                                            <?php

                                            echo number_format(
                                                (float)$order['total_price'],
                                                2
                                            );

                                            ?> €

                                        </strong>

                                    </div>


                                </div>


                            </div>


                            <!-- ORDER FOOTER -->

                            <div class="admin-order-card-footer">


                                <!-- STATUS -->

                                <form
                                    method="POST"
                                    class="admin-status-form"
                                >

                                    <input
                                        type="hidden"
                                        name="order_id"
                                        value="<?php echo (int)$order['id']; ?>"
                                    >


                                    <label>
                                        Statusi
                                    </label>


                                    <select
                                        name="status"
                                        class="order-status-select <?php echo htmlspecialchars($order['status']); ?>"
                                        onchange="this.form.submit()"
                                    >

                                        <?php foreach ($status_names as $key => $name): ?>

                                            <option
                                                value="<?php echo $key; ?>"
                                                <?php echo $order['status'] === $key ? 'selected' : ''; ?>
                                            >

                                                <?php echo $name; ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>


                                    <input
                                        type="hidden"
                                        name="update_status"
                                        value="1"
                                    >

                                </form>


                                <!-- DELETE -->

                                <form
                                    method="POST"
                                    onsubmit="return confirm('A jeni të sigurt që dëshironi ta fshini këtë porosi?');"
                                >

                                    <input
                                        type="hidden"
                                        name="order_id"
                                        value="<?php echo (int)$order['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="delete_order"
                                        class="admin-delete-order"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Fshi porosinë

                                    </button>

                                </form>


                            </div>


                        </article>


                    <?php endwhile; ?>


                <?php else: ?>


                    <div class="admin-no-orders">


                        <div>

                            <i class="fa-solid fa-cart-shopping"></i>

                        </div>


                        <h3>
                            Nuk u gjet asnjë porosi
                        </h3>


                        <p>

                            <?php if ($search !== '' || $filter_status !== ''): ?>

                                Provo të ndryshosh filtrat.

                            <?php else: ?>

                                Kur klientët të bëjnë porosi,
                                ato do të shfaqen këtu.

                            <?php endif; ?>

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


</div>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>