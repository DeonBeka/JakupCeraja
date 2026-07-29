<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';


// ==========================================
// GET AUTHOR ID
// ==========================================

$author_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;


// ==========================================
// GET AUTHOR
// ==========================================

$author_query = "

    SELECT
        id,
        fullname,
        photo,
        biography,
        created_at

    FROM authors

    WHERE id = $author_id

    LIMIT 1

";


$author_result = mysqli_query(
    $conn,
    $author_query
);


// ==========================================
// AUTHOR NOT FOUND
// ==========================================

if (
    !$author_result ||
    mysqli_num_rows($author_result) === 0
) {

    http_response_code(404);

    ?>

    <main>

        <section class="author-not-found">

            <div class="container">

                <div class="author-not-found-inner">

                    <i class="fa-solid fa-user-slash"></i>

                    <h1>
                        Autori nuk u gjet
                    </h1>

                    <p>
                        Na vjen keq, por autori që po kërkoni
                        nuk ekziston ose është larguar.
                    </p>

                    <a
                        href="authors.php"
                        class="btn btn-primary"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Kthehu te Autorët

                    </a>

                </div>

            </div>

        </section>

    </main>

    <?php

    include 'includes/footer.php';

    exit;
}


$author = mysqli_fetch_assoc(
    $author_result
);


// ==========================================
// GET AUTHOR'S BOOKS
// ==========================================

$books_query = "

    SELECT

        books.id,
        books.title,
        books.cover,
        books.description,
        books.publish_date,

        categories.name AS category_name

    FROM books

    LEFT JOIN categories
        ON books.category_id = categories.id

    WHERE books.author_id = $author_id

    ORDER BY books.publish_date DESC, books.id DESC

";


$books_result = mysqli_query(
    $conn,
    $books_query
);


// Count books

$books_count = 0;

if ($books_result) {

    $books_count =
        mysqli_num_rows($books_result);

}

?>

<main>


    <!-- =========================================
         AUTHOR HERO
    ========================================== -->

    <section class="author-details-section">

        <div class="container">


            <!-- BREADCRUMB -->

            <div class="author-breadcrumb">

                <a href="index.php">
                    Ballina
                </a>

                <i class="fa-solid fa-chevron-right"></i>

                <a href="authors.php">
                    Autorët
                </a>

                <i class="fa-solid fa-chevron-right"></i>

                <span>

                    <?php
                    echo htmlspecialchars(
                        $author['fullname']
                    );
                    ?>

                </span>

            </div>


            <!-- AUTHOR PROFILE -->

            <div class="author-profile">


                <!-- PHOTO -->

                <div class="author-profile-photo">

                    <?php if (!empty($author['photo'])): ?>

                        <img
                            src="assets/uploads/<?php
                                echo htmlspecialchars(
                                    $author['photo']
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $author['fullname']
                                );
                            ?>"
                        >

                    <?php else: ?>

                        <div class="author-profile-placeholder">

                            <i class="fa-solid fa-user"></i>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- INFO -->

                <div class="author-profile-content">

                    <span class="section-tag">
                        AUTOR
                    </span>


                    <h1>

                        <?php
                        echo htmlspecialchars(
                            $author['fullname']
                        );
                        ?>

                    </h1>


                    <?php if (!empty($author['biography'])): ?>

                        <p class="author-profile-biography">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $author['biography']
                                )
                            );
                            ?>

                        </p>

                    <?php else: ?>

                        <p class="author-profile-biography">

                            Biografia e autorit do të
                            publikohet së shpejti.

                        </p>

                    <?php endif; ?>


                    <!-- AUTHOR STATS -->

                    <div class="author-profile-stats">

                        <div>

                            <strong>
                                <?php
                                echo $books_count;
                                ?>
                            </strong>

                            <span>
                                <?php

                                echo (
                                    $books_count == 1
                                )
                                ? 'Libër'
                                : 'Libra';

                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>
                                Jakup Ceraja
                            </strong>

                            <span>
                                Shtëpia Botuese
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================
         AUTHOR BOOKS
    ========================================== -->

    <section class="author-books-section">

        <div class="container">


            <div class="author-books-heading">

                <div>

                    <span class="section-tag">
                        KRIJIMTARIA
                    </span>

                    <h2>
                        Librat e autorit
                    </h2>

                </div>

                <a
                    href="books.php"
                    class="author-books-all"
                >

                    Të gjithë librat

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <?php if ($books_result && $books_count > 0): ?>


                <div class="author-books-grid">


                    <?php while (
                        $book =
                        mysqli_fetch_assoc(
                            $books_result
                        )
                    ): ?>


                        <!-- BOOK -->

                        <article class="author-book-card">


                            <!-- COVER -->

                            <div class="author-book-cover">

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

                                    <div class="author-book-placeholder">

                                        <i class="fa-solid fa-book-open"></i>

                                    </div>

                                <?php endif; ?>


                                <div class="author-book-overlay">

                                    <a
                                        href="book-details.php?id=<?php
                                            echo (int)$book['id'];
                                        ?>"
                                        aria-label="Shiko librin"
                                    >

                                        <i class="fa-solid fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>


                            <!-- CONTENT -->

                            <div class="author-book-content">


                                <?php if (!empty($book['category_name'])): ?>

                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            $book['category_name']
                                        );
                                        ?>

                                    </span>

                                <?php endif; ?>


                                <h3>

                                    <?php
                                    echo htmlspecialchars(
                                        $book['title']
                                    );
                                    ?>

                                </h3>


                                <?php if (!empty($book['publish_date'])): ?>

                                    <p>

                                        <i class="fa-regular fa-calendar"></i>

                                        <?php

                                        echo date(
                                            'd.m.Y',
                                            strtotime(
                                                $book['publish_date']
                                            )
                                        );

                                        ?>

                                    </p>

                                <?php endif; ?>


                                <a
                                    href="book-details.php?id=<?php
                                        echo (int)$book['id'];
                                    ?>"
                                >

                                    Shiko librin

                                    <i class="fa-solid fa-arrow-right"></i>

                                </a>

                            </div>

                        </article>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <!-- NO BOOKS -->

                <div class="author-no-books">

                    <div>

                        <i class="fa-solid fa-book-open"></i>

                    </div>

                    <h3>
                        Ende nuk ka libra të publikuar
                    </h3>

                    <p>
                        Librat e këtij autori do të
                        shfaqen këtu sapo të publikohen.
                    </p>

                </div>


            <?php endif; ?>


        </div>

    </section>


    <!-- =========================================
         BACK CTA
    ========================================== -->

    <section class="author-details-cta">

        <div class="container">

            <div class="author-details-cta-inner">

                <div>

                    <span class="section-tag">
                        SHTËPIA BOTUESE "JAKUP CERAJA"
                    </span>

                    <h2>
                        Zbuloni më shumë
                        nga autorët tanë.
                    </h2>

                </div>


                <a
                    href="authors.php"
                    class="btn btn-primary"
                >

                    Shiko Autorët

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