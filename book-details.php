<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';


// ==========================================
// GET BOOK ID
// ==========================================

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


// ==========================================
// GET BOOK
// ==========================================

$book_query = "

    SELECT

        books.id,
        books.title,
        books.cover,
        books.description,
        books.publish_date,
        books.featured,

        authors.id AS author_id,
        authors.fullname AS author_name,
        authors.photo AS author_photo,
        authors.biography AS author_biography,

        categories.id AS category_id,
        categories.name AS category_name,
        categories.icon AS category_icon

    FROM books

    LEFT JOIN authors
        ON books.author_id = authors.id

    LEFT JOIN categories
        ON books.category_id = categories.id

    WHERE books.id = $book_id

    LIMIT 1

";


$book_result = mysqli_query(
    $conn,
    $book_query
);


// ==========================================
// CHECK BOOK
// ==========================================

if (
    !$book_result ||
    mysqli_num_rows($book_result) === 0
) {

    http_response_code(404);

    ?>

    <main>

        <section class="book-not-found">

            <div class="container">

                <div class="book-not-found-inner">

                    <i class="fa-solid fa-book-open"></i>

                    <h1>
                        Libri nuk u gjet
                    </h1>

                    <p>
                        Na vjen keq, por libri që po kërkoni
                        nuk ekziston ose është larguar.
                    </p>

                    <a
                        href="books.php"
                        class="btn btn-primary"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Kthehu te Librat

                    </a>

                </div>

            </div>

        </section>

    </main>

    <?php

    include 'includes/footer.php';

    exit;
}


$book = mysqli_fetch_assoc(
    $book_result
);

?>

<main>


    <!-- ==========================================
         BOOK DETAILS HERO
    =========================================== -->

    <section class="book-details-section">

        <div class="container">


            <!-- BREADCRUMB -->

            <div class="book-breadcrumb">

                <a href="index.php">
                    Ballina
                </a>

                <i class="fa-solid fa-chevron-right"></i>

                <a href="books.php">
                    Librat
                </a>

                <i class="fa-solid fa-chevron-right"></i>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $book['title']
                    );
                    ?>
                </span>

            </div>


            <!-- BOOK -->

            <div class="book-details-grid">


                <!-- ==================================
                     COVER
                =================================== -->

                <div class="book-details-cover">

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
                        >

                    <?php else: ?>

                        <div class="book-details-placeholder">

                            <i class="fa-solid fa-book-open"></i>

                            <span>
                                Shtëpia Botuese
                                "Jakup Ceraja"
                            </span>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- ==================================
                     INFORMATION
                =================================== -->

                <div class="book-details-content">


                    <!-- CATEGORY -->

                    <?php if (!empty($book['category_name'])): ?>

                        <span class="book-details-category">

                            <?php if (!empty($book['category_icon'])): ?>

                                <i class="<?php
                                    echo htmlspecialchars(
                                        $book['category_icon']
                                    );
                                ?>"></i>

                            <?php endif; ?>

                            <?php
                            echo htmlspecialchars(
                                $book['category_name']
                            );
                            ?>

                        </span>

                    <?php endif; ?>


                    <!-- TITLE -->

                    <h1>

                        <?php
                        echo htmlspecialchars(
                            $book['title']
                        );
                        ?>

                    </h1>


                    <!-- AUTHOR -->

                    <?php if (!empty($book['author_name'])): ?>

                        <div class="book-details-author">

                            <?php if (!empty($book['author_photo'])): ?>

                                <img
                                    src="assets/uploads/<?php
                                        echo htmlspecialchars(
                                            $book['author_photo']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $book['author_name']
                                        );
                                    ?>"
                                >

                            <?php else: ?>

                                <div class="author-small-placeholder">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                            <?php endif; ?>


                            <div>

                                <span>
                                    Autor
                                </span>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $book['author_name']
                                    );
                                    ?>

                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- META -->

                    <div class="book-details-meta">


                        <?php if (!empty($book['publish_date'])): ?>

                            <div>

                                <i class="fa-regular fa-calendar"></i>

                                <span>
                                    Publikuar më
                                </span>

                                <strong>

                                    <?php

                                    echo date(
                                        'd.m.Y',
                                        strtotime(
                                            $book['publish_date']
                                        )
                                    );

                                    ?>

                                </strong>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($book['category_name'])): ?>

                            <div>

                                <i class="fa-solid fa-layer-group"></i>

                                <span>
                                    Kategoria
                                </span>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $book['category_name']
                                    );
                                    ?>

                                </strong>

                            </div>

                        <?php endif; ?>


                    </div>


                    <!-- DESCRIPTION -->

                    <?php if (!empty($book['description'])): ?>

                        <div class="book-details-description">

                            <h2>
                                Për librin
                            </h2>

                            <p>

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $book['description']
                                    )
                                );
                                ?>

                            </p>

                        </div>

                    <?php endif; ?>


                  <!-- BUTTONS -->

                    <div class="book-details-buttons">

                        <a
                            href="books.php"
                            class="btn btn-primary"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                                Të gjithë librat

                        </a>


                        <a
                            href="buy.php?id=<?php echo (int)$book['id']; ?>"
                            class="btn book-buy-btn"
                        >

                            <i class="fa-solid fa-cart-shopping"></i>

                                Bli librin

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ==========================================
         AUTHOR SECTION
    =========================================== -->

    <?php if (!empty($book['author_name'])): ?>

        <section class="book-author-section">

            <div class="container">

                <div class="book-author-box">


                    <!-- AUTHOR PHOTO -->

                    <div class="book-author-photo">

                        <?php if (!empty($book['author_photo'])): ?>

                            <img
                                src="assets/uploads/<?php
                                    echo htmlspecialchars(
                                        $book['author_photo']
                                    );
                                ?>"
                                alt="<?php
                                    echo htmlspecialchars(
                                        $book['author_name']
                                    );
                                ?>"
                                loading="lazy"
                            >

                        <?php else: ?>

                            <div class="author-large-placeholder">

                                <i class="fa-solid fa-user"></i>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- AUTHOR INFO -->

                    <div class="book-author-info">

                        <span class="section-tag">
                            RRETH AUTORIT
                        </span>

                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $book['author_name']
                            );
                            ?>

                        </h2>


                        <?php if (!empty($book['author_biography'])): ?>

                            <p>

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $book['author_biography']
                                    )
                                );
                                ?>

                            </p>

                        <?php else: ?>

                            <p>
                                Biografia e autorit do të
                                publikohet së shpejti.
                            </p>

                        <?php endif; ?>


                        <?php if (!empty($book['author_id'])): ?>

                            <a
                                href="author-details.php?id=<?php
                                    echo (int)$book['author_id'];
                                ?>"
                                class="author-details-link"
                            >

                                Shiko profilin e autorit

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </section>

    <?php endif; ?>


</main>


<?php include 'includes/footer.php'; ?>


<script src="assets/js/script.js"></script>

</body>

</html>