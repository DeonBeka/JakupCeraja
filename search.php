<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

$search = trim($_GET['q'] ?? '');

$books = [];
$authors = [];
$news = [];

if (!empty($search)) {

    $searchTerm = "%" . $search . "%";


    // =========================================
    // SEARCH BOOKS
    // =========================================

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT
            books.id,
            books.title,
            books.cover,
            books.description,
            books.publish_date,
            authors.fullname AS author_name,
            categories.name AS category_name
        FROM books

        LEFT JOIN authors
            ON books.author_id = authors.id

        LEFT JOIN categories
            ON books.category_id = categories.id

        WHERE
            books.title LIKE ?
            OR books.description LIKE ?
            OR authors.fullname LIKE ?
            OR categories.name LIKE ?

        ORDER BY books.created_at DESC
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    mysqli_stmt_close($stmt);


    // =========================================
    // SEARCH AUTHORS
    // =========================================

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            fullname,
            photo,
            biography

        FROM authors

        WHERE
            fullname LIKE ?
            OR biography LIKE ?

        ORDER BY created_at DESC
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = $row;
    }

    mysqli_stmt_close($stmt);


    // =========================================
    // SEARCH NEWS
    // =========================================

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            title,
            image,
            content,
            created_at

        FROM news

        WHERE
            title LIKE ?
            OR content LIKE ?

        ORDER BY created_at DESC
        "
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $news[] = $row;
    }

    mysqli_stmt_close($stmt);

}

?>

<main>

    <!-- =========================================
         SEARCH HERO
    ========================================== -->

    <section class="page-hero search-page-hero">

        <div class="container">

            <span class="section-tag">
                KËRKO
            </span>

            <h1>
                Kërko në botimet tona
            </h1>

            <p>
                Gjeni libra, autorë dhe lajme nga
                Shtëpia Botuese "Jakup Ceraja".
            </p>

        </div>

    </section>


    <!-- =========================================
         SEARCH FORM
    ========================================== -->

    <section class="search-section">

        <div class="container">

            <form
                action="search.php"
                method="GET"
                class="search-page-form"
            >

                <div class="search-input-wrapper">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        name="q"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Kërko libër, autor, lajm..."
                        autocomplete="off"
                    >

                </div>

                <button type="submit">
                    Kërko
                </button>

            </form>


            <?php if (!empty($search)): ?>

                <div class="search-results-heading">

                    <span class="section-tag">
                        REZULTATET
                    </span>

                    <h2>
                        Rezultatet për:
                        <strong>
                            "<?php echo htmlspecialchars($search); ?>"
                        </strong>
                    </h2>

                </div>


                <!-- =================================
                     BOOK RESULTS
                ================================== -->

                <?php if (!empty($books)): ?>

                    <section class="search-results-group">

                        <div class="search-group-title">

                            <h3>
                                <i class="fa-solid fa-book"></i>
                                Libra
                            </h3>

                            <span>
                                <?php echo count($books); ?>
                            </span>

                        </div>


                        <div class="search-books-grid">

                            <?php foreach ($books as $book): ?>

                                <article class="search-book-card">

                                    <a
                                        href="book-details.php?id=<?php echo (int)$book['id']; ?>"
                                        class="search-book-cover"
                                    >

                                        <?php if (!empty($book['cover'])): ?>

                                            <img
                                                src="assets/images/books/<?php echo htmlspecialchars($book['cover']); ?>"
                                                alt="<?php echo htmlspecialchars($book['title']); ?>"
                                                loading="lazy"
                                            >

                                        <?php else: ?>

                                            <div class="search-placeholder-cover">

                                                <i class="fa-solid fa-book-open"></i>

                                            </div>

                                        <?php endif; ?>

                                    </a>


                                    <div class="search-book-info">

                                        <span class="search-result-category">

                                            <?php

                                            echo !empty($book['category_name'])
                                                ? htmlspecialchars($book['category_name'])
                                                : 'Botim';

                                            ?>

                                        </span>


                                        <h4>

                                            <a
                                                href="book-details.php?id=<?php echo (int)$book['id']; ?>"
                                            >

                                                <?php echo htmlspecialchars($book['title']); ?>

                                            </a>

                                        </h4>


                                        <?php if (!empty($book['author_name'])): ?>

                                            <p class="search-author">

                                                <?php echo htmlspecialchars($book['author_name']); ?>

                                            </p>

                                        <?php endif; ?>


                                        <a
                                            href="book-details.php?id=<?php echo (int)$book['id']; ?>"
                                            class="search-read-more"
                                        >

                                            Shiko librin

                                            <i class="fa-solid fa-arrow-right"></i>

                                        </a>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endif; ?>


                <!-- =================================
                     AUTHORS
                ================================== -->

                <?php if (!empty($authors)): ?>

                    <section class="search-results-group">

                        <div class="search-group-title">

                            <h3>

                                <i class="fa-solid fa-pen-nib"></i>

                                Autorë

                            </h3>

                            <span>
                                <?php echo count($authors); ?>
                            </span>

                        </div>


                        <div class="search-authors-grid">

                            <?php foreach ($authors as $author): ?>

                                <article class="search-author-card">


                                    <div class="search-author-photo">

                                        <?php if (!empty($author['photo'])): ?>

                                            <img
                                                src="assets/images/authors/<?php echo htmlspecialchars($author['photo']); ?>"
                                                alt="<?php echo htmlspecialchars($author['fullname']); ?>"
                                                loading="lazy"
                                            >

                                        <?php else: ?>

                                            <div class="search-author-placeholder">

                                                <i class="fa-solid fa-user"></i>

                                            </div>

                                        <?php endif; ?>

                                    </div>


                                    <div class="search-author-info">

                                        <h4>

                                            <?php echo htmlspecialchars($author['fullname']); ?>

                                        </h4>


                                        <?php if (!empty($author['biography'])): ?>

                                            <p>

                                                <?php

                                                echo htmlspecialchars(
                                                    mb_substr(
                                                        strip_tags($author['biography']),
                                                        0,
                                                        130
                                                    )
                                                );

                                                ?>...

                                            </p>

                                        <?php endif; ?>


                                        <a
                                            href="author-details.php?id=<?php echo (int)$author['id']; ?>"
                                        >

                                            Shiko profilin

                                            <i class="fa-solid fa-arrow-right"></i>

                                        </a>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endif; ?>


                <!-- =================================
                     NEWS
                ================================== -->

                <?php if (!empty($news)): ?>

                    <section class="search-results-group">

                        <div class="search-group-title">

                            <h3>

                                <i class="fa-solid fa-newspaper"></i>

                                Lajme

                            </h3>

                            <span>
                                <?php echo count($news); ?>
                            </span>

                        </div>


                        <div class="search-news-grid">

                            <?php foreach ($news as $item): ?>

                                <article class="search-news-card">


                                    <?php if (!empty($item['image'])): ?>

                                        <a
                                            href="news-detail.php?id=<?php echo (int)$item['id']; ?>"
                                            class="search-news-image"
                                        >

                                            <img
                                                src="assets/images/news/<?php echo htmlspecialchars($item['image']); ?>"
                                                alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                loading="lazy"
                                            >

                                        </a>

                                    <?php endif; ?>


                                    <div class="search-news-info">

                                        <span>

                                            <?php

                                            echo date(
                                                'd.m.Y',
                                                strtotime($item['created_at'])
                                            );

                                            ?>

                                        </span>


                                        <h4>

                                            <a
                                                href="news-detail.php?id=<?php echo (int)$item['id']; ?>"
                                            >

                                                <?php echo htmlspecialchars($item['title']); ?>

                                            </a>

                                        </h4>


                                        <p>

                                            <?php

                                            echo htmlspecialchars(
                                                mb_substr(
                                                    strip_tags($item['content']),
                                                    0,
                                                    150
                                                )
                                            );

                                            ?>...

                                        </p>


                                        <a
                                            href="news-detail.php?id=<?php echo (int)$item['id']; ?>"
                                            class="search-read-more"
                                        >

                                            Lexo më shumë

                                            <i class="fa-solid fa-arrow-right"></i>

                                        </a>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endif; ?>


                <!-- =================================
                     NO RESULTS
                ================================== -->

                <?php if (
                    empty($books) &&
                    empty($authors) &&
                    empty($news)
                ): ?>

                    <div class="search-no-results">

                        <div class="search-no-results-icon">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </div>


                        <h3>
                            Nuk u gjet asnjë rezultat
                        </h3>


                        <p>
                            Nuk gjetëm libra, autorë ose lajme
                            që përputhen me kërkimin tuaj.
                        </p>


                        <a
                            href="search.php"
                            class="search-reset"
                        >

                            Pastro kërkimin

                        </a>

                    </div>

                <?php endif; ?>


            <?php else: ?>


                <!-- =================================
                     EMPTY SEARCH
                ================================== -->

                <div class="search-empty">

                    <i class="fa-solid fa-book-open"></i>

                    <h3>
                        Çfarë po kërkoni?
                    </h3>

                    <p>
                        Shkruani emrin e një libri,
                        autori ose lajmi në fushën më sipër.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>