<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';


// ==========================================
// GET BOOK ID
// ==========================================

$book_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


// ==========================================
// GET BOOK
// ==========================================

$book_query = "
    SELECT

        books.id,
        books.title,
        books.price,
        books.cover,

        authors.fullname AS author_name

    FROM books

    LEFT JOIN authors
        ON books.author_id = authors.id

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
                        Na vjen keq, por ky libër nuk ekziston.
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


// ==========================================
// DEFAULT VALUES
// ==========================================

$fullname = '';
$email = '';
$phone = '';
$country = '';
$city = '';
$address = '';

$shipping_price = 0;
$total_price = (float)$book['price'];

$success_message = '';
$error_message = '';


// ==========================================
// FORM SUBMISSION
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim(
        $_POST['fullname'] ?? ''
    );

    $email = trim(
        $_POST['email'] ?? ''
    );

    $phone = trim(
        $_POST['phone'] ?? ''
    );

    $country = trim(
        $_POST['country'] ?? ''
    );

    $city = trim(
        $_POST['city'] ?? ''
    );

    $address = trim(
        $_POST['address'] ?? ''
    );


    // ======================================
    // SHIPPING
    // ======================================

    if ($country === 'Kosovë') {

        $shipping_price = 0;

    } elseif (
        $country === 'Shqipëri' ||
        $country === 'Maqedoni e Veriut'
    ) {

        $shipping_price = 3;

    } else {

        $shipping_price = 0;

    }


    $total_price =
        (float)$book['price']
        + $shipping_price;


    // ======================================
    // VALIDATION
    // ======================================

    if (
        empty($fullname) ||
        empty($phone) ||
        empty($country) ||
        empty($city) ||
        empty($address)
    ) {

        $error_message =
            'Ju lutem plotësoni të gjitha fushat e detyrueshme.';

    } else {


        // ==================================
        // INSERT ORDER
        // ==================================

        $stmt = mysqli_prepare(
            $conn,

            "
            INSERT INTO orders
            (
                book_id,
                fullname,
                email,
                phone,
                country,
                city,
                address,
                book_price,
                shipping_price,
                total_price,
                status
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'new'
            )
            "
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "issssssddd",

                $book_id,
                $fullname,
                $email,
                $phone,
                $country,
                $city,
                $address,
                $book['price'],
                $shipping_price,
                $total_price
            );


            if (mysqli_stmt_execute($stmt)) {

                $success_message =
                    'Porosia juaj u pranua me sukses! Do t’ju kontaktojmë në telefon për konfirmim.';


                // Clear form

                $fullname = '';
                $email = '';
                $phone = '';
                $country = '';
                $city = '';
                $address = '';

            } else {

                $error_message =
                    'Ndodhi një gabim gjatë ruajtjes së porosisë. Ju lutem provoni përsëri.';

            }


            mysqli_stmt_close($stmt);

        } else {

            $error_message =
                'Nuk mund të përpunohet porosia aktualisht.';

        }

    }

}

?>

<main>


    <!-- ==========================================
         BUY HERO
    =========================================== -->

    <section class="buy-page-hero">

        <div class="container">

            <span class="section-tag">
                POROSIT LIBRIN
            </span>

            <h1>
                Bli librin tuaj
            </h1>

            <p>
                Plotësoni të dhënat dhe ne do t'jua
                dërgojmë librin me postë.
            </p>

        </div>

    </section>



    <!-- ==========================================
         BUY SECTION
    =========================================== -->

    <section class="buy-section">

        <div class="container">

            <div class="buy-layout">


                <!-- =================================
                     BOOK SUMMARY
                ================================== -->

                <div class="buy-book">


                    <div class="buy-book-cover">

                         <?php if (!empty($book['cover'])): ?>

                        <img
                            src="<?php
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

                            <div class="buy-book-placeholder">

                                <i class="fa-solid fa-book-open"></i>

                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="buy-book-info">

                        <span>
                            LIBRI
                        </span>

                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $book['title']
                            );
                            ?>

                        </h2>


                        <?php if (!empty($book['author_name'])): ?>

                            <p>

                                <?php
                                echo htmlspecialchars(
                                    $book['author_name']
                                );
                                ?>

                            </p>

                        <?php endif; ?>


                        <div class="buy-price">

                            <span>
                                Çmimi
                            </span>

                            <strong>
                                <?php
                                echo number_format(
                                    (float)$book['price'],
                                    2
                                );
                                ?> €
                            </strong>

                        </div>

                    </div>

                </div>



                <!-- =================================
                     ORDER FORM
                ================================== -->

                <div class="buy-form-wrapper">


                    <div class="buy-form-header">

                        <span class="section-tag">
                            TË DHËNAT E DËRGESËS
                        </span>

                        <h2>
                            Ku dëshironi ta dërgojmë?
                        </h2>

                        <p>
                            Nuk nevojitet kartelë bankare.
                            Pagesa bëhet me para në dorë
                            kur ta pranoni porosinë.
                        </p>

                    </div>



                    <!-- SUCCESS -->

                    <?php if (!empty($success_message)): ?>

                        <div class="buy-alert success">

                            <i class="fa-solid fa-circle-check"></i>

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    $success_message
                                );
                                ?>

                            </span>

                        </div>

                    <?php endif; ?>



                    <!-- ERROR -->

                    <?php if (!empty($error_message)): ?>

                        <div class="buy-alert error">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    $error_message
                                );
                                ?>

                            </span>

                        </div>

                    <?php endif; ?>



                    <!-- FORM -->

                    <form
                        method="POST"
                        action="buy.php?id=<?php echo (int)$book['id']; ?>"
                        class="buy-form"
                    >


                        <!-- NAME -->

                        <div class="buy-form-group">

                            <label for="fullname">
                                Emri dhe Mbiemri *
                            </label>

                            <input
                                type="text"
                                id="fullname"
                                name="fullname"
                                value="<?php
                                    echo htmlspecialchars(
                                        $fullname
                                    );
                                ?>"
                                placeholder="Emri dhe mbiemri"
                                required
                            >

                        </div>



                        <!-- EMAIL -->

                        <div class="buy-form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php
                                    echo htmlspecialchars(
                                        $email
                                    );
                                ?>"
                                placeholder="email@example.com"
                            >

                        </div>



                        <!-- PHONE -->

                        <div class="buy-form-group">

                            <label for="phone">
                                Numri i telefonit *
                            </label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?php
                                    echo htmlspecialchars(
                                        $phone
                                    );
                                ?>"
                                placeholder="+383 XX XXX XXX"
                                required
                            >

                        </div>



                        <!-- COUNTRY -->

                        <div class="buy-form-group">

                            <label for="country">
                                Shteti *
                            </label>

                            <select
                                id="country"
                                name="country"
                                required
                            >

                                <option value="">
                                    Zgjidhni shtetin
                                </option>

                                <option
                                    value="Kosovë"
                                    <?php
                                    echo $country === 'Kosovë'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Kosovë — Posta FALAS
                                </option>

                                <option
                                    value="Shqipëri"
                                    <?php
                                    echo $country === 'Shqipëri'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Shqipëri — Posta 3 €
                                </option>

                                <option
                                    value="Maqedoni e Veriut"
                                    <?php
                                    echo $country === 'Maqedoni e Veriut'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Maqedoni e Veriut — Posta 3 €
                                </option>

                            </select>

                        </div>



                        <!-- CITY -->

                        <div class="buy-form-group">

                            <label for="city">
                                Qyteti *
                            </label>

                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="<?php
                                    echo htmlspecialchars(
                                        $city
                                    );
                                ?>"
                                placeholder="Qyteti"
                                required
                            >

                        </div>



                        <!-- ADDRESS -->

                        <div class="buy-form-group full">

                            <label for="address">
                                Adresa *
                            </label>

                            <input
                                type="text"
                                id="address"
                                name="address"
                                value="<?php
                                    echo htmlspecialchars(
                                        $address
                                    );
                                ?>"
                                placeholder="Rruga, numri i shtëpisë..."
                                required
                            >

                        </div>



                        <!-- =================================
                             ORDER SUMMARY
                        ================================== -->

                        <div class="buy-summary">

                            <div>

                                <span>
                                    Çmimi i librit
                                </span>

                                <strong id="bookPrice">

                                    <?php
                                    echo number_format(
                                        (float)$book['price'],
                                        2
                                    );
                                    ?> €

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Posta
                                </span>

                                <strong id="shippingPrice">

                                    0.00 €

                                </strong>

                            </div>


                            <div class="buy-total">

                                <span>
                                    TOTALI
                                </span>

                                <strong id="totalPrice">

                                    <?php
                                    echo number_format(
                                        (float)$book['price'],
                                        2
                                    );
                                    ?> €

                                </strong>

                            </div>

                        </div>



                        <!-- SUBMIT -->

                        <button
                            type="submit"
                            class="buy-submit"
                        >

                            <i class="fa-solid fa-cart-shopping"></i>

                            Porosit librin

                        </button>


                        <p class="cash-payment-note">

                            <i class="fa-solid fa-money-bill-wave"></i>

                            Pagesa bëhet me para në dorë
                            gjatë pranimit të dërgesës.

                        </p>

                    </form>

                </div>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const country =
            document.getElementById('country');

        const shippingPrice =
            document.getElementById('shippingPrice');

        const totalPrice =
            document.getElementById('totalPrice');

        const bookPrice =
            <?php echo (float)$book['price']; ?>;


        function updatePrice() {

            let shipping = 0;


            if (
                country.value === 'Shqipëri' ||
                country.value === 'Maqedoni e Veriut'
            ) {

                shipping = 3;

            }


            const total =
                bookPrice + shipping;


            shippingPrice.textContent =
                shipping.toFixed(2) + ' €';


            totalPrice.textContent =
                total.toFixed(2) + ' €';

        }


        country.addEventListener(
            'change',
            updatePrice
        );


        updatePrice();

    }
);

</script>


<script src="assets/js/script.js"></script>

</body>
</html>