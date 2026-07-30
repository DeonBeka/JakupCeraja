<?php

require_once '../includes/auth.php';
require_once '../includes/database.php';


// ==========================================
// GET GALLERY
// ==========================================

$sql = "
    SELECT
        id,
        image,
        title,
        created_at
    FROM gallery
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gabim në databazë: " . mysqli_error($conn));
}

$gallery = [];

while ($item = mysqli_fetch_assoc($result)) {
    $gallery[] = $item;
}

$total_gallery = count($gallery);

?>

<!DOCTYPE html>
<html lang="sq">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Galeria | Jakup Ceraja Admin</title>

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


    <!-- SIDEBAR -->

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
                <span>Dashboard</span>
            </a>

            <a href="books.php">
                <i class="fa-solid fa-book"></i>
                <span>Libra</span>
            </a>

            <a href="authors.php">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Autorë</span>
            </a>

            <a href="categories.php">
                <i class="fa-solid fa-layer-group"></i>
                <span>Kategori</span>
            </a>

            <a href="news.php">
                <i class="fa-solid fa-newspaper"></i>
                <span>Lajme</span>
            </a>

            <a
                href="gallery.php"
                class="active"
            >
                <i class="fa-solid fa-images"></i>
                <span>Galeri</span>
            </a>

            <a href="messages.php">
                <i class="fa-solid fa-envelope"></i>
                <span>Mesazhe</span>
            </a>

            <a href="orders.php">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Porosi</span>
            </a>

            <a href="settings.php">
                <i class="fa-solid fa-gear"></i>
                <span>Cilësime</span>
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



    <!-- MAIN -->

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
                    Galeria
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
                    href="../gallery.php"
                    target="_blank"
                    class="admin-view-site"
                >

                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                    Shiko galerinë

                </a>

            </div>

        </header>



        <!-- CONTENT -->

        <div class="admin-content">


            <!-- INTRO -->

            <div class="admin-welcome">

                <div>

                    <span>
                        MENAXHIMI I GALERISË
                    </span>

                    <h2>
                        Galeria
                    </h2>

                    <p>
                        Shto, ndrysho dhe menaxho fotografitë
                        e Shtëpisë Botuese "Jakup Ceraja".
                    </p>

                </div>


                <div class="admin-welcome-icon">

                    <i class="fa-solid fa-images"></i>

                </div>

            </div>



            <!-- STATS -->

            <div class="gallery-stats">

                <div class="gallery-stat-card">

                    <div class="gallery-stat-icon">

                        <i class="fa-solid fa-images"></i>

                    </div>

                    <div>

                        <span>
                            Gjithsej fotografi
                        </span>

                        <strong>
                            <?php echo $total_gallery; ?>
                        </strong>

                    </div>

                </div>

            </div>



            <!-- GALLERY PANEL -->

            <section class="admin-panel-card gallery-panel">


                <div class="admin-panel-header">

                    <div>

                        <span>
                            GALERIA
                        </span>

                        <h3>
                            Fotografitë
                        </h3>

                    </div>


                    <a
                        href="add-gallery.php"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Shto fotografi

                    </a>

                </div>



                <!-- SEARCH -->

                <div class="gallery-toolbar">

                    <div class="gallery-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="gallerySearch"
                            placeholder="Kërko fotografi..."
                            autocomplete="off"
                        >

                    </div>

                </div>



                <!-- GRID -->

                <div
                    class="admin-gallery-grid"
                    id="galleryGrid"
                >


                    <?php if (empty($gallery)): ?>


                        <div class="gallery-empty">

                            <div class="gallery-empty-icon">

                                <i class="fa-solid fa-images"></i>

                            </div>

                            <h3>
                                Galeria është bosh
                            </h3>

                            <p>
                                Shto fotografinë e parë nga butoni
                                "Shto fotografi".
                            </p>

                            <a
                                href="add-gallery.php"
                                class="admin-primary-button"
                            >

                                <i class="fa-solid fa-plus"></i>

                                Shto fotografi

                            </a>

                        </div>


                    <?php else: ?>


                        <?php foreach ($gallery as $item): ?>


                            <article
                                class="admin-gallery-card"
                                data-title="<?php
                                    echo htmlspecialchars(
                                        strtolower(
                                            $item['title'] ?? ''
                                        )
                                    );
                                ?>"
                            >


                                <!-- IMAGE -->

                                <div class="admin-gallery-image">

                                    <img
                                        src="../assets/uploads/gallery/<?php
                                            echo htmlspecialchars(
                                                $item['image']
                                            );
                                        ?>"
                                        alt="<?php
                                            echo htmlspecialchars(
                                                $item['title']
                                            );
                                        ?>"
                                        loading="lazy"
                                    >


                                    <div class="admin-gallery-overlay">

                                        <a
                                            href="../assets/uploads/gallery/<?php
                                                echo htmlspecialchars(
                                                    $item['image']
                                                );
                                            ?>"
                                            target="_blank"
                                            class="gallery-overlay-button"
                                            title="Shiko fotografinë"
                                        >

                                            <i class="fa-solid fa-eye"></i>

                                        </a>

                                    </div>

                                </div>



                                <!-- INFO -->

                                <div class="admin-gallery-info">


                                    <div>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $item['title']
                                                ?: 'Pa titull'
                                            );

                                            ?>

                                        </strong>

                                        <span>

                                            <?php

                                            echo date(
                                                'd.m.Y',
                                                strtotime(
                                                    $item['created_at']
                                                )
                                            );

                                            ?>

                                        </span>

                                    </div>



                                    <!-- ACTIONS -->

                                    <div class="gallery-actions">


                                        <a
                                            href="edit-gallery.php?id=<?php
                                                echo (int)$item['id'];
                                            ?>"
                                            class="gallery-action edit"
                                            title="Ndrysho"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                        </a>


                                        <a
                                            href="delete-gallery.php?id=<?php
                                                echo (int)$item['id'];
                                            ?>"
                                            class="gallery-action delete"
                                            title="Fshi"
                                            onclick="
                                                return confirm(
                                                    'A jeni i sigurt që dëshironi ta fshini këtë fotografi?'
                                                );
                                            "
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </a>


                                    </div>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    <?php endif; ?>


                </div>



                <!-- NO SEARCH -->

                <div
                    class="gallery-no-results"
                    id="galleryNoResults"
                >

                    <i class="fa-solid fa-images"></i>

                    <h3>
                        Nuk u gjet asgjë
                    </h3>

                    <p>
                        Provo një titull tjetër.
                    </p>

                </div>


            </section>


        </div>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const search =
            document.getElementById('gallerySearch');

        const grid =
            document.getElementById('galleryGrid');

        const noResults =
            document.getElementById('galleryNoResults');

        if (!search || !grid) {
            return;
        }


        const cards =
            grid.querySelectorAll(
                '.admin-gallery-card'
            );


        search.addEventListener(
            'input',
            function () {

                const value =
                    search.value
                        .toLowerCase()
                        .trim();

                let visible = 0;


                cards.forEach(function (card) {

                    const title =
                        card.dataset.title || '';


                    if (title.includes(value)) {

                        card.style.display = '';

                        visible++;

                    } else {

                        card.style.display = 'none';

                    }

                });


                if (noResults) {

                    noResults.style.display =
                        visible === 0
                        ? 'block'
                        : 'none';

                }

            }
        );

    }
);

</script>


<script src="../assets/js/admin.js"></script>

<script src="../assets/js/darkmode.js"></script>


</body>

</html>