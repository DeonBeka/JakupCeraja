<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET CATEGORIES
// ==========================================

$sql = "
    SELECT
        categories.id,
        categories.name,
        categories.icon,
        categories.created_at,
        COUNT(books.id) AS books_count

    FROM categories

    LEFT JOIN books
        ON books.category_id = categories.id

    GROUP BY
        categories.id,
        categories.name,
        categories.icon,
        categories.created_at

    ORDER BY categories.name ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}


$categories = [];

while ($category = mysqli_fetch_assoc($result)) {
    $categories[] = $category;
}


// ==========================================
// STATISTICS
// ==========================================

$total_categories = count($categories);

$total_books = 0;

foreach ($categories as $category) {
    $total_books += (int)$category['books_count'];
}

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
        Kategoritë | Jakup Ceraja Admin
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


            <a
                href="categories.php"
                class="active"
            >

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


            <a href="orders.php">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Porosi
                </span>

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
                    ADMIN PANEL
                </span>

                <h1>
                    Kategoritë
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
                    href="../books.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Shiko librat

                </a>


            </div>


        </header>



        <!-- ======================================
             CONTENT
        ======================================= -->

        <div class="admin-content">


            <!-- PAGE INTRO -->

            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I KATEGORIVE
                    </span>

                    <h2>
                        Kategoritë
                    </h2>

                    <p>
                        Krijo dhe menaxho kategoritë e
                        librave të Shtëpisë Botuese
                        "Jakup Ceraja".
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-layer-group"></i>

                </div>

            </div>



            <!-- ==================================
                 STATISTICS
            =================================== -->

            <div class="categories-stats">


                <div class="categories-stat-card">

                    <div class="categories-stat-icon">

                        <i class="fa-solid fa-layer-group"></i>

                    </div>

                    <div>

                        <span>
                            Gjithsej kategori
                        </span>

                        <strong>
                            <?php
                            echo $total_categories;
                            ?>
                        </strong>

                    </div>

                </div>


                <div class="categories-stat-card">

                    <div class="categories-stat-icon">

                        <i class="fa-solid fa-book"></i>

                    </div>

                    <div>

                        <span>
                            Libra në kategori
                        </span>

                        <strong>
                            <?php
                            echo $total_books;
                            ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- ==================================
                 CATEGORY PANEL
            =================================== -->

            <section class="admin-panel-card categories-panel">


                <div class="admin-panel-header">


                    <div>

                        <span>
                            KATEGORITË
                        </span>

                        <h3>
                            Lista e kategorive
                        </h3>

                    </div>


                    <a
                        href="add-category.php"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Shto kategori

                    </a>


                </div>



                <!-- SEARCH -->

                <div class="categories-toolbar">


                    <div class="categories-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="categorySearch"
                            placeholder="Kërko kategori..."
                            autocomplete="off"
                        >

                    </div>


                </div>



                <!-- CATEGORY GRID -->

                <div
                    class="categories-grid"
                    id="categoriesGrid"
                >


                    <?php if (empty($categories)): ?>


                        <div class="categories-empty">

                            <div class="categories-empty-icon">

                                <i class="fa-solid fa-layer-group"></i>

                            </div>

                            <strong>
                                Nuk ka kategori ende
                            </strong>

                            <span>
                                Shto kategorinë e parë nga
                                butoni "Shto kategori".
                            </span>

                        </div>


                    <?php else: ?>


                        <?php foreach ($categories as $category): ?>


                            <article
                                class="category-admin-card"
                                data-name="<?php
                                    echo htmlspecialchars(
                                        strtolower(
                                            $category['name']
                                        )
                                    );
                                ?>"
                            >


                                <!-- ICON -->

                                <div class="category-admin-icon">

                                    <?php if (!empty($category['icon'])): ?>

                                        <i class="<?php
                                            echo htmlspecialchars(
                                                $category['icon']
                                            );
                                        ?>"></i>

                                    <?php else: ?>

                                        <i class="fa-solid fa-layer-group"></i>

                                    <?php endif; ?>

                                </div>



                                <!-- INFO -->

                                <div class="category-admin-info">


                                    <span class="category-admin-label">
                                        KATEGORI
                                    </span>


                                    <h3>

                                        <?php
                                        echo htmlspecialchars(
                                            $category['name']
                                        );
                                        ?>

                                    </h3>


                                    <p>

                                        <i class="fa-solid fa-book"></i>

                                        <?php
                                        echo (int)$category['books_count'];
                                        ?>

                                        <?php
                                        echo (
                                            (int)$category['books_count'] === 1
                                            ? 'libër'
                                            : 'libra'
                                        );
                                        ?>

                                    </p>


                                </div>



                                <!-- ACTIONS -->

                                <div class="category-admin-actions">


                                    <a
                                        href="edit-category.php?id=<?php
                                            echo (int)$category['id'];
                                        ?>"
                                        class="category-action edit"
                                        title="Ndrysho kategorinë"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <a
                                        href="delete-category.php?id=<?php
                                            echo (int)$category['id'];
                                        ?>"
                                        class="category-action delete"
                                        title="Fshi kategorinë"
                                        onclick="return confirm('A jeni i sigurt që dëshironi ta fshini këtë kategori?');"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </a>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    <?php endif; ?>


                </div>



                <!-- NO SEARCH RESULTS -->

                <div
                    class="categories-no-results"
                    id="categoriesNoResults"
                    style="display:none;"
                >

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <strong>
                        Nuk u gjet asnjë kategori
                    </strong>

                    <span>
                        Provo një emër tjetër.
                    </span>

                </div>


            </section>


        </div>


    </main>


</div>



<!-- ======================================
     SEARCH JAVASCRIPT
======================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const search =
            document.getElementById('categorySearch');

        const cards =
            document.querySelectorAll(
                '.category-admin-card'
            );

        const noResults =
            document.getElementById(
                'categoriesNoResults'
            );


        if (!search) {
            return;
        }


        function filterCategories() {


            const value =
                search.value
                    .toLowerCase()
                    .trim();


            let visible =
                0;


            cards.forEach(
                function (card) {


                    const name =
                        card.dataset.name || '';


                    if (
                        name.includes(value)
                    ) {

                        card.style.display = '';

                        visible++;

                    } else {

                        card.style.display = 'none';

                    }

                }
            );


            if (
                noResults &&
                cards.length > 0
            ) {

                noResults.style.display =
                    visible === 0
                    ? 'flex'
                    : 'none';

            }

        }


        search.addEventListener(
            'input',
            filterCategories
        );


    }
);

</script>



<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>