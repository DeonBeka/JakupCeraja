<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<main>

    <!-- ==============================
         PAGE HERO
    ============================== -->

    <section class="page-hero books-page-hero">

        <div class="container">

            <span class="section-tag">
                SHTËPIA BOTUESE "JAKUP CERAJA"
            </span>

            <h1>
                Librat
            </h1>

            <p>
                Zbuloni botimet tona, autorët dhe veprat
                që pasurojnë kulturën dhe letërsinë shqiptare.
            </p>

        </div>

    </section>


    <!-- ==============================
         BOOKS
    ============================== -->

    <section class="books-page-section">

        <div class="container">


            <!-- SEARCH + FILTER -->

            <div class="books-toolbar">

                <div class="books-search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        id="bookSearch"
                        placeholder="Kërko libër ose autor..."
                        autocomplete="off"
                    >

                </div>


                <div class="books-filter">

                    <select id="categoryFilter">

                        <option value="all">
                            Të gjitha kategoritë
                        </option>


                        <?php

                        $category_query = "
                            SELECT id, name
                            FROM categories
                            ORDER BY name ASC
                        ";

                        $category_result = mysqli_query(
                            $conn,
                            $category_query
                        );


                        if (
                            $category_result &&
                            mysqli_num_rows($category_result) > 0
                        ):

                            while (
                                $category =
                                mysqli_fetch_assoc(
                                    $category_result
                                )
                            ):

                        ?>

                            <option
                                value="<?php echo htmlspecialchars($category['name']); ?>"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $category['name']
                                );
                                ?>
                            </option>

                        <?php

                            endwhile;

                        endif;

                        ?>

                    </select>

                </div>

            </div>


            <!-- BOOKS GRID -->

            <div
                class="books-grid"
                id="booksGrid"
            >

                <?php

                /*
                ==========================================
                BOOKS + AUTHORS + CATEGORIES
                ==========================================
                */

                $books_query = "

                    SELECT

                        books.id,
                        books.title,
                        books.cover,
                        books.description,
                        books.publish_date,
                        books.featured,

                        authors.fullname AS author_name,

                        categories.name AS category_name

                    FROM books

                    LEFT JOIN authors
                        ON books.author_id = authors.id

                    LEFT JOIN categories
                        ON books.category_id = categories.id

                    ORDER BY books.id DESC

                ";


                $books_result = mysqli_query(
                    $conn,
                    $books_query
                );


                if (
                    $books_result &&
                    mysqli_num_rows($books_result) > 0
                ):

                    while (
                        $book =
                        mysqli_fetch_assoc(
                            $books_result
                        )
                    ):

                ?>


                    <!-- BOOK CARD -->

                    <article
                        class="book-card"

                        data-category="<?php
                            echo htmlspecialchars(
                                $book['category_name'] ?? ''
                            );
                        ?>"

                        data-title="<?php
                            echo htmlspecialchars(
                                $book['title'] ?? ''
                            );
                        ?>"

                        data-author="<?php
                            echo htmlspecialchars(
                                $book['author_name'] ?? ''
                            );
                        ?>"
                    >


                        <!-- COVER -->

                        <div class="book-card-cover">


                            <?php if (!empty($book['cover'])): ?>

                                <img
                                    src="assets/uploads/<?php
                                        echo htmlspecialchars(
                                            $book['cover']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $book['title']
                                        );
                                    ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="book-placeholder">

                                    <i class="fa-solid fa-book-open"></i>

                                    <span>
                                        Shtëpia Botuese
                                        "Jakup Ceraja"
                                    </span>

                                </div>

                            <?php endif; ?>


                            <!-- HOVER BUTTON -->

                            <div class="book-card-overlay">

                                <a
                                    href="book-details.php?id=<?php
                                        echo (int)$book['id'];
                                    ?>"
                                    class="book-view-button"
                                    aria-label="Shiko librin"
                                >

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </div>


                        <!-- BOOK CONTENT -->

                        <div class="book-card-content">


                            <!-- CATEGORY -->

                            <?php if (!empty($book['category_name'])): ?>

                                <span class="book-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $book['category_name']
                                    );
                                    ?>

                                </span>

                            <?php endif; ?>


                            <!-- TITLE -->

                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $book['title']
                                );
                                ?>

                            </h3>


                            <!-- AUTHOR -->

                            <p class="book-author">

                                <i class="fa-solid fa-pen-nib"></i>

                                <?php

                                echo htmlspecialchars(
                                    $book['author_name']
                                    ?? 'Autor i panjohur'
                                );

                                ?>

                            </p>


                            <!-- DESCRIPTION -->

                            <?php if (!empty($book['description'])): ?>

                                <p class="book-description">

                                    <?php

                                    $description =
                                        $book['description'];

                                    echo htmlspecialchars(
                                        mb_substr(
                                            $description,
                                            0,
                                            120
                                        )
                                    );

                                    if (
                                        mb_strlen(
                                            $description
                                        ) > 120
                                    ) {
                                        echo '...';
                                    }

                                    ?>

                                </p>

                            <?php endif; ?>


                            <!-- DATE -->

                            <?php if (!empty($book['publish_date'])): ?>

                                <div class="book-date">

                                    <i class="fa-regular fa-calendar"></i>

                                    <?php

                                    echo date(
                                        'd.m.Y',
                                        strtotime(
                                            $book['publish_date']
                                        )
                                    );

                                    ?>

                                </div>

                            <?php endif; ?>


                            <!-- DETAILS -->

                            <a
                                href="book-details.php?id=<?php
                                    echo (int)$book['id'];
                                ?>"
                                class="book-read-more"
                            >

                                Shiko librin

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </div>

                    </article>


                <?php

                    endwhile;

                else:

                ?>


                    <!-- EMPTY -->

                    <div class="books-empty">

                        <div class="books-empty-icon">

                            <i class="fa-solid fa-book-open"></i>

                        </div>

                        <h3>
                            Librat do të shfaqen së shpejti
                        </h3>

                        <p>
                            Aktualisht nuk ka libra në katalog.
                        </p>

                    </div>


                <?php endif; ?>

            </div>


            <!-- NO SEARCH RESULTS -->

            <div
                class="books-no-results"
                id="booksNoResults"
            >

                <i class="fa-solid fa-book-open"></i>

                <h3>
                    Nuk u gjet asnjë libër
                </h3>

                <p>
                    Provo një titull, autor ose kategori tjetër.
                </p>

            </div>

        </div>

    </section>


    <!-- ==============================
         CTA
    ============================== -->

    <section class="books-cta">

        <div class="container">

            <div class="books-cta-inner">

                <div>

                    <span class="section-tag">
                        SHTËPIA BOTUESE "JAKUP CERAJA"
                    </span>

                    <h2>
                        Një libër i mirë
                        gjithmonë ia vlen.
                    </h2>

                    <p>
                        Zbuloni botimet tona dhe gjeni
                        librin tuaj të radhës.
                    </p>

                </div>


                <a
                    href="contact.php"
                    class="btn btn-primary"
                >

                    Na Kontaktoni

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>


<script src="assets/js/script.js"></script>

</body>
</html>