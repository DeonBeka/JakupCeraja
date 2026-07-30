<?php
include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main>

   <section class="hero-slider">

    <div class="hero-slides">

        <?php

        $hero_query = "
            SELECT *
            FROM hero_slides
            WHERE is_active = 1
            ORDER BY sort_order ASC, id ASC
        ";

        $hero_result = mysqli_query($conn, $hero_query);

        $slide_number = 0;

        if ($hero_result && mysqli_num_rows($hero_result) > 0):

            while ($slide = mysqli_fetch_assoc($hero_result)):

                $active_class = ($slide_number === 0) ? 'active' : '';

        ?>

        <article class="hero-slide <?php echo $active_class; ?>">

            <div class="container hero-slide-content">

                <div class="hero-text">

                    <span class="hero-subtitle">
                        Shtëpia Botuese "Jakup Ceraja"
                    </span>

                    <h1>
                        <?php echo htmlspecialchars($slide['title']); ?>
                    </h1>

                    <p>
                        <?php echo htmlspecialchars($slide['subtitle']); ?>
                    </p>

                    <?php if (!empty($slide['button_text'])): ?>

                        <a
                            href="<?php echo htmlspecialchars($slide['button_link']); ?>"
                            class="btn btn-primary"
                        >
                            <?php echo htmlspecialchars($slide['button_text']); ?>

                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    <?php endif; ?>

                </div>

                <div class="hero-image">

                    <img
                        src="assets/images/hero/<?php echo htmlspecialchars($slide['image']); ?>"
                        alt="<?php echo htmlspecialchars($slide['title']); ?>"
                    >

                </div>

            </div>

        </article>

        <?php

                $slide_number++;

            endwhile;

        else:

        ?>

            <article class="hero-slide active">

                <div class="container hero-slide-content">

                    <div class="hero-text">

                        <span class="hero-subtitle">
                            Shtëpia Botuese "Jakup Ceraja"
                        </span>

                        <h1>
                            Libra që ruajnë kulturën dhe frymëzojnë breza.
                        </h1>

                        <p>
                            Zbuloni botimet tona dhe pasurinë e letërsisë
                            shqiptare dhe botërore.
                        </p>

                        <a href="books.php" class="btn btn-primary">
                            Shfleto Librat
                        </a>

                    </div>

                </div>

            </article>

        <?php endif; ?>

    </div>


    <?php if (isset($slide_number) && $slide_number > 1): ?>

        <button class="hero-arrow hero-prev" type="button">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <button class="hero-arrow hero-next" type="button">
            <i class="fa-solid fa-chevron-right"></i>
        </button>


        <div class="hero-dots">

            <?php for ($i = 0; $i < $slide_number; $i++): ?>

                <button
                    type="button"
                    class="hero-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                    data-slide="<?php echo $i; ?>"
                    aria-label="Shfaq slide <?php echo $i + 1; ?>"
                ></button>

            <?php endfor; ?>

        </div>

    <?php endif; ?>

    </section>
    
    <section class="about">

    <div class="container about-container">

        <div class="about-image">

            <img src="assets/images/backgrounds/about-books.jpg" alt="Rreth Nesh">

        </div>

        <div class="about-content">

            <span class="section-tag">
                RRETH NESH
            </span>

            <h2>
                Traditë, kulturë dhe pasion për librin.
            </h2>

            <p>
                Shtëpia Botuese "Jakup Ceraja" është një shtëpi botuese nga Kosova
                e përkushtuar botimit të veprave me vlerë letrare, kulturore,
                historike dhe edukative.
            </p>

            <p>
                Ne besojmë se libri është ura që lidh brezat. Përmes botimeve tona
                promovojmë autorët shqiptarë dhe sjellim përkthime cilësore nga
                letërsia botërore.
            </p>

            <div class="about-cards">

                <div class="about-card">

                    <i class="fa-solid fa-book"></i>

                    <h3>Botime Cilësore</h3>

                    <p>Libra të përzgjedhur me standarde të larta botimi.</p>

                </div>

                <div class="about-card">

                    <i class="fa-solid fa-earth-europe"></i>

                    <h3>Përkthime</h3>

                    <p>Vepra të njohura botërore në gjuhën shqipe.</p>

                </div>

            </div>

        </div>

    </div>

    </section>
    <section class="featured-books">

    <div class="container">

        <div class="section-heading">

            <span class="section-tag">
                BOTIMET TONA
            </span>

            <h2>
                Librat e Veçuar
            </h2>

            <p>
                Zbuloni disa nga botimet tona të përzgjedhura.
            </p>

        </div>

        <div class="books-grid">

            <?php

            $books_query = "
                SELECT
                    books.*,
                    authors.fullname AS author_name,
                    categories.name AS category_name
                FROM books
                LEFT JOIN authors
                    ON books.author_id = authors.id
                LEFT JOIN categories
                    ON books.category_id = categories.id
                WHERE books.featured = 1
                ORDER BY books.id DESC
                LIMIT 6
            ";

            $books_result = mysqli_query($conn, $books_query);

            if ($books_result && mysqli_num_rows($books_result) > 0):

                while ($book = mysqli_fetch_assoc($books_result)):

            ?>

                <article class="book-card">

                    <div class="book-cover">

                        <img
                           src="<?php echo htmlspecialchars($book['cover']); ?>"
                            alt="<?php echo htmlspecialchars($book['title']); ?>"
                        >

                        <span class="book-category">
                            <?php echo htmlspecialchars($book['category_name']); ?>
                        </span>

                    </div>

                    <div class="book-content">

                        <h3>
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h3>

                        <p class="book-author">
                            <?php echo htmlspecialchars($book['author_name']); ?>
                        </p>

                        <p class="book-description">
                            <?php
                            echo htmlspecialchars(
                                mb_strimwidth(
                                    $book['description'],
                                    0,
                                    120,
                                    '...'
                                )
                            );
                            ?>
                        </p>

                        <a
                            href="book-details.php?id=<?php echo $book['id']; ?>"
                            class="book-link"
                        >
                            Lexo më shumë
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </article>

            <?php

                endwhile;

            else:

            ?>

                <p class="no-books">
                    Aktualisht nuk ka libra të publikuar.
                </p>

            <?php endif; ?>

        </div>

        <div class="books-button">

            <a href="books.php" class="btn btn-primary">
                Shiko të gjithë librat
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

    </section>
   <section class="categories-section">

    <div class="container">

        <div class="section-heading">

            <span class="section-tag">
                ZBULO BOTIMET
            </span>

            <h2>
                Kategoritë
            </h2>

            <p>
                Eksploroni botimet tona sipas kategorive.
            </p>

        </div>


        <div class="categories-grid">

            <?php

            /*
            ==========================================
            SHFAQ VETËM 6 KATEGORITË
            ME MË SHUMË LIBRA
            ==========================================
            */

            $categories_query = "

                SELECT

                    categories.id,
                    categories.name,
                    categories.icon,

                    COUNT(books.id) AS book_count

                FROM categories

                LEFT JOIN books
                    ON books.category_id = categories.id

                GROUP BY
                    categories.id,
                    categories.name,
                    categories.icon

                ORDER BY
                    book_count DESC,
                    categories.id ASC

                LIMIT 6

            ";


            $categories_result = mysqli_query(
                $conn,
                $categories_query
            );


            if (
                $categories_result &&
                mysqli_num_rows($categories_result) > 0
            ):

                while (
                    $category =
                    mysqli_fetch_assoc(
                        $categories_result
                    )
                ):

            ?>

                <a
                    href="books.php?category=<?php
                        echo (int)$category['id'];
                    ?>"
                    class="category-card"
                >

                    <div class="category-icon">

                        <?php if (!empty($category['icon'])): ?>

                            <i class="<?php
                                echo htmlspecialchars(
                                    $category['icon']
                                );
                            ?>"></i>

                        <?php else: ?>

                            <i class="fa-solid fa-book"></i>

                        <?php endif; ?>

                    </div>


                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $category['name']
                        );
                        ?>

                    </h3>


                    <span class="category-arrow">

                        <i class="fa-solid fa-arrow-right"></i>

                    </span>

                </a>


            <?php

                endwhile;

            endif;

            ?>

        </div>

    </div>

</section>
    <section class="why-us">

    <div class="container">

        <div class="section-heading">

            <span class="section-tag">
                PSE NE?
            </span>

            <h2>
                Pse të zgjidhni Jakup Ceraja?
            </h2>

            <p>
                Përkushtimi ynë ndaj librit dhe kulturës është në qendër të çdo botimi.
            </p>

        </div>

        <div class="why-grid">

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>

                <h3>Cilësi Botimi</h3>

                <p>
                    Kujdes i veçantë në përzgjedhjen dhe përgatitjen e çdo botimi.
                </p>

            </div>

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-earth-europe"></i>
                </div>

                <h3>Përkthime Ndërkombëtare</h3>

                <p>
                    Sjellim në gjuhën shqipe vepra dhe autorë të njohur nga bota.
                </p>

            </div>

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-pen-nib"></i>
                </div>

                <h3>Autorë Shqiptarë</h3>

                <p>
                    Mbështesim dhe promovojmë krijimtarinë e autorëve shqiptarë.
                </p>

            </div>

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>

                <h3>Dashuri për Librin</h3>

                <p>
                    Libri dhe kultura e leximit janë në themel të punës sonë.
                </p>

            </div>

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-award"></i>
                </div>

                <h3>Profesionalizëm</h3>

                <p>
                    Punojmë me përkushtim për të ofruar botime të denja për lexuesit.
                </p>

            </div>

            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>

                <h3>Bashkëpunim me Autorë</h3>

                <p>
                    Krijojmë marrëdhënie të qëndrueshme dhe profesionale me autorët.
                </p>

            </div>

        </div>

    </div>

    </section> 
    <section class="latest-publications">

    <div class="container">

        <div class="latest-header">

            <div>

                <span class="section-tag">
                    BOTIMET E FUNDIT
                </span>

                <h2>
                    Publikimet më të reja
                </h2>

                <p>
                    Zbuloni librat më të fundit të botuar nga
                    Shtëpia Botuese "Jakup Ceraja".
                </p>

            </div>

            <a href="books.php" class="latest-view-all">

                Shiko të gjithë librat

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <div class="latest-books">

            <?php

            $latest_query = "
                SELECT
                    books.*,
                    authors.fullname AS author_name,
                    categories.name AS category_name
                FROM books

                LEFT JOIN authors
                    ON books.author_id = authors.id

                LEFT JOIN categories
                    ON books.category_id = categories.id

                ORDER BY books.publish_date DESC, books.id DESC

                LIMIT 4
            ";

            $latest_result = mysqli_query($conn, $latest_query);


            if ($latest_result && mysqli_num_rows($latest_result) > 0):

                while ($book = mysqli_fetch_assoc($latest_result)):

            ?>

                <article class="latest-book-card">


                    <div class="latest-book-image">

                        <img
                            src="<?php echo htmlspecialchars($book['cover']); ?>"
                            alt="<?php echo htmlspecialchars($book['title']); ?>"
                        >


                        <span class="latest-date">

                            <i class="fa-regular fa-calendar"></i>

                            <?php
                            echo date(
                                'd.m.Y',
                                strtotime($book['publish_date'])
                            );
                            ?>

                        </span>

                    </div>


                    <div class="latest-book-content">


                        <span class="latest-category">

                            <?php
                            echo htmlspecialchars(
                                $book['category_name']
                            );
                            ?>

                        </span>


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $book['title']
                            );
                            ?>

                        </h3>


                        <p class="latest-author">

                            <i class="fa-solid fa-pen-nib"></i>

                            <?php
                            echo htmlspecialchars(
                                $book['author_name']
                            );
                            ?>

                        </p>


                        <a
                            href="book-details.php?id=<?php echo $book['id']; ?>"
                            class="latest-link"
                        >

                            Lexo më shumë

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                    </div>

                </article>

            <?php

                endwhile;

            else:

            ?>

                <p class="no-books">
                    Aktualisht nuk ka publikime.
                </p>

            <?php endif; ?>

        </div>

    </div>

    </section>
    <!-- =========================================
     FEATURED AUTHORS
========================================= -->

<section class="featured-authors">

    <div class="container">

        <!-- SECTION HEADER -->

        <div class="section-header">

            <div>

                <span class="section-tag">
                    AUTORËT TANË
                </span>

                <h2>
                    Zëra që pasurojnë letërsinë
                </h2>

                <p>
                    Njihuni me autorët dhe krijimtarinë
                    që janë pjesë e botimeve tona.
                </p>

            </div>

            <a
                href="authors.php"
                class="section-link"
            >

                Shiko të gjithë autorët

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <!-- AUTHORS -->

        <div class="featured-authors-grid">

            <?php

            $featured_authors_query = "

                SELECT
                    id,
                    fullname,
                    photo,
                    biography

                FROM authors

                ORDER BY id DESC

                LIMIT 4

            ";

            $featured_authors_result = mysqli_query(
                $conn,
                $featured_authors_query
            );


            if (
                $featured_authors_result &&
                mysqli_num_rows($featured_authors_result) > 0
            ):

                while (
                    $author =
                    mysqli_fetch_assoc(
                        $featured_authors_result
                    )
                ):

            ?>

                <!-- AUTHOR CARD -->

                <article class="featured-author-card">

                    <!-- PHOTO -->

                    <div class="featured-author-photo">

                        <?php if (!empty($author['photo'])): ?>

                            <img
                                src="assets/uploads/authors/<?php
                                    echo htmlspecialchars(
                                        $author['photo']
                                    );
                                ?>"
                                alt="<?php
                                    echo htmlspecialchars(
                                        $author['fullname']
                                    );
                                ?>"
                                loading="lazy"
                            >

                        <?php else: ?>

                            <div class="featured-author-placeholder">

                                <i class="fa-solid fa-user"></i>

                            </div>

                        <?php endif; ?>


                        <!-- OVERLAY -->

                        <div class="featured-author-overlay">

                            <a
                                href="author-details.php?id=<?php
                                    echo (int)$author['id'];
                                ?>"
                                aria-label="Shiko autorin"
                            >

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </div>

                    </div>


                    <!-- CONTENT -->

                    <div class="featured-author-content">

                        <span>
                            AUTOR
                        </span>

                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $author['fullname']
                            );
                            ?>

                        </h3>


                        <?php if (!empty($author['biography'])): ?>

                            <p>

                                <?php

                                $short_bio =
                                    $author['biography'];

                                echo htmlspecialchars(
                                    mb_substr(
                                        $short_bio,
                                        0,
                                        110
                                    )
                                );

                                if (
                                    mb_strlen($short_bio) > 110
                                ) {
                                    echo '...';
                                }

                                ?>

                            </p>

                        <?php else: ?>

                            <p>
                                Autor i Shtëpisë Botuese
                                "Jakup Ceraja".
                            </p>

                        <?php endif; ?>


                        <a
                            href="author-details.php?id=<?php
                                echo (int)$author['id'];
                            ?>"
                        >

                            Shiko profilin

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </article>


            <?php

                endwhile;

            else:

            ?>

                <div class="featured-authors-empty">

                    <i class="fa-solid fa-user-pen"></i>

                    <h3>
                        Autorët do të shfaqen së shpejti
                    </h3>

                    <p>
                        Aktualisht nuk ka autorë të regjistruar.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

    </section> 
   <section class="gallery-section">

    <div class="container">

        <div class="section-heading">

            <span class="section-tag">
                NGA AKTIVITETET TONA
            </span>

            <h2>
                Galeria
            </h2>

            <p>
                Momente nga aktivitetet, promovimet dhe
                botimet e Shtëpisë Botuese "Jakup Ceraja".
            </p>

        </div>


        <div class="gallery-grid">

            <?php

            $gallery_query = "
                SELECT id, image, title, created_at
                FROM gallery
                ORDER BY id DESC
                LIMIT 12
            ";

            $gallery_result = mysqli_query(
                $conn,
                $gallery_query
            );


            if (
                $gallery_result &&
                mysqli_num_rows($gallery_result) > 0
            ):

                while (
                    $gallery_item =
                    mysqli_fetch_assoc($gallery_result)
                ):

            ?>

                <article class="gallery-item">

                    <img
                        src="assets/uploads/gallery/<?php echo htmlspecialchars($gallery_item['image']); ?>"
                        alt="<?php echo htmlspecialchars($gallery_item['title']); ?>"
                        loading="lazy"
                    >


                    <div class="gallery-overlay">

                        <div class="gallery-info">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $gallery_item['title']
                                );
                                ?>
                            </h3>

                        </div>


                        <button
                            type="button"
                            class="gallery-view"
                            data-image="assets/uploads/gallery/<?php echo htmlspecialchars($gallery_item['image']); ?>"
                            data-title="<?php echo htmlspecialchars($gallery_item['title']); ?>"
                            aria-label="Shiko fotografinë"
                        >

                            <i class="fa-solid fa-expand"></i>

                        </button>

                    </div>

                </article>

            <?php

                endwhile;

            else:

            ?>

                <p class="no-books">
                    Aktualisht nuk ka fotografi në galeri.
                </p>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- GALLERY LIGHTBOX -->

<div
    class="gallery-lightbox"
    id="galleryLightbox"
>

    <button
        type="button"
        class="lightbox-close"
        aria-label="Mbyll"
    >

        <i class="fa-solid fa-xmark"></i>

    </button>


    <div class="lightbox-content">

        <img
            src=""
            alt=""
            id="lightboxImage"
        >

        <h3 id="lightboxTitle"></h3>

    </div>

</div>
</main>

<?php
include 'includes/footer.php';
?>