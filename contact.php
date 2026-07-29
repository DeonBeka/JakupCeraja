<?php

include 'includes/database.php';
include 'includes/header.php';
include 'includes/navbar.php';


// =========================================
// FORM VARIABLES
// =========================================

$success_message = '';
$error_message = '';

$fullname = '';
$email = '';
$phone = '';
$subject = '';
$message = '';


// =========================================
// FORM SUBMISSION
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');


    // =====================================
    // VALIDATION
    // =====================================

    if (
        empty($fullname) ||
        empty($email) ||
        empty($message)
    ) {

        $error_message =
            'Ju lutem plotësoni të gjitha fushat e detyrueshme.';

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error_message =
            'Ju lutem vendosni një email të vlefshëm.';

    } else {


        // =================================
        // INSERT MESSAGE
        // =================================

        $stmt = mysqli_prepare(
            $conn,
            "
            INSERT INTO messages
            (
                fullname,
                email,
                phone,
                subject,
                message,
                status
            )
            VALUES (?, ?, ?, ?, ?, 'unread')
            "
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $fullname,
                $email,
                $phone,
                $subject,
                $message
            );


            if (mysqli_stmt_execute($stmt)) {

                $success_message =
                    'Faleminderit! Mesazhi juaj u dërgua me sukses. Do t’ju kontaktojmë sa më shpejt.';


                // Clear form after success

                $fullname = '';
                $email = '';
                $phone = '';
                $subject = '';
                $message = '';

            } else {

                $error_message =
                    'Ndodhi një problem gjatë dërgimit të mesazhit. Ju lutem provoni përsëri.';

            }


            mysqli_stmt_close($stmt);

        } else {

            $error_message =
                'Nuk mund të përpunohet kërkesa aktualisht.';

        }

    }

}

?>

<main>


    <!-- =========================================
         CONTACT HERO
    ========================================== -->

    <section class="page-hero contact-page-hero">

        <div class="container">

            <span class="section-tag">
                NA KONTAKTONI
            </span>

            <h1>
                Jemi këtu për ju
            </h1>

            <p>
                Për pyetje rreth botimeve, autorëve,
                bashkëpunimeve apo çdo informacioni tjetër,
                na kontaktoni.
            </p>

        </div>

    </section>



    <!-- =========================================
         CONTACT SECTION
    ========================================== -->

    <section class="contact-section">

        <div class="container">

            <div class="contact-layout">


                <!-- =================================
                     CONTACT INFORMATION
                ================================== -->

                <div class="contact-info">


                    <span class="section-tag">
                        INFORMACIONE
                    </span>


                    <h2>
                        Flasim për libra,
                        ide dhe bashkëpunime.
                    </h2>


                    <p class="contact-intro">

                        Shtëpia Botuese "Jakup Ceraja"
                        është e hapur për bashkëpunime
                        me autorë, institucione dhe
                        lexues.

                    </p>



                    <!-- PHONE -->

                    <div class="contact-info-item">

                        <div class="contact-icon">

                            <i class="fa-solid fa-phone"></i>

                        </div>


                        <div>

                            <span>
                                TELEFON
                            </span>

                            <a href="tel:+38300000000">

                                +383 00 000 000

                            </a>

                        </div>

                    </div>



                    <!-- EMAIL -->

                    <div class="contact-info-item">

                        <div class="contact-icon">

                            <i class="fa-solid fa-envelope"></i>

                        </div>


                        <div>

                            <span>
                                EMAIL
                            </span>

                            <a href="mailto:info@jakupceraja.com">

                                info@jakupceraja.com

                            </a>

                        </div>

                    </div>



                    <!-- ADDRESS -->

                    <div class="contact-info-item">

                        <div class="contact-icon">

                            <i class="fa-solid fa-location-dot"></i>

                        </div>


                        <div>

                            <span>
                                ADRESA
                            </span>

                            <p>
                                Prishtinë, Kosovë
                            </p>

                        </div>

                    </div>



                    <!-- SOCIAL MEDIA -->

                    <div class="contact-social">

                        <span>
                            NA NDIQNI
                        </span>


                        <div>


                            <!-- FACEBOOK -->

                            <a
                                href="https://www.facebook.com/ShtepiaBotueseJakupCeraja"
                                target="_blank"
                                rel="noopener"
                                aria-label="Facebook"
                            >

                                <i class="fa-brands fa-facebook-f"></i>

                            </a>



                            <!-- INSTAGRAM -->

                            <a
                                href="#"
                                aria-label="Instagram"
                            >

                                <i class="fa-brands fa-instagram"></i>

                            </a>


                        </div>

                    </div>

                </div>



                <!-- =================================
                     CONTACT FORM
                ================================== -->

                <div class="contact-form-wrapper">


                    <!-- FORM HEADER -->

                    <div class="contact-form-header">

                        <span class="section-tag">
                            MESAZHI JUAJ
                        </span>


                        <h2>
                            Na shkruani
                        </h2>


                        <p>
                            Plotësoni formularin dhe
                            ekipi ynë do t'ju përgjigjet.
                        </p>

                    </div>



                    <!-- =================================
                         SUCCESS MESSAGE
                    ================================== -->

                    <?php if (!empty($success_message)): ?>

                        <div class="contact-alert success">

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



                    <!-- =================================
                         ERROR MESSAGE
                    ================================== -->

                    <?php if (!empty($error_message)): ?>

                        <div class="contact-alert error">

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



                    <!-- =================================
                         FORM
                    ================================== -->

                    <form
                        action="contact.php"
                        method="POST"
                        class="contact-form"
                    >



                        <!-- FULL NAME -->

                        <div class="form-group">

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

                        <div class="form-group">

                            <label for="email">

                                Email *

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
                                required
                            >

                        </div>



                        <!-- PHONE -->

                        <div class="form-group">

                            <label for="phone">

                                Telefoni

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
                            >

                        </div>



                        <!-- SUBJECT -->

                        <div class="form-group">

                            <label for="subject">

                                Subjekti

                            </label>


                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                value="<?php

                                    echo htmlspecialchars(
                                        $subject
                                    );

                                ?>"
                                placeholder="Subjekti i mesazhit"
                            >

                        </div>



                        <!-- MESSAGE -->

                        <div class="form-group full">

                            <label for="message">

                                Mesazhi *

                            </label>


                            <textarea
                                id="message"
                                name="message"
                                rows="7"
                                placeholder="Shkruani mesazhin tuaj..."
                                required
                            ><?php

                                echo htmlspecialchars(
                                    $message
                                );

                            ?></textarea>

                        </div>



                        <!-- SUBMIT BUTTON -->

                        <button
                            type="submit"
                            class="contact-submit"
                        >

                            <span>
                                Dërgo Mesazhin
                            </span>

                            <i class="fa-solid fa-arrow-right"></i>

                        </button>


                    </form>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================
         GOOGLE MAP
    ========================================== -->

    <section class="contact-map-section">

        <div class="container">


            <!-- MAP HEADER -->

            <div class="contact-map-header">

                <span class="section-tag">

                    NA GJENI

                </span>


                <h2>

                    Vizitoni shtëpinë tonë botuese

                </h2>

            </div>



            <!-- MAP -->

            <div class="contact-map">

                <iframe
                    src="https://www.google.com/maps?q=Pristina,Kosovo&output=embed"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Lokacioni i Shtëpisë Botuese Jakup Ceraja"
                ></iframe>

            </div>


        </div>

    </section>


</main>



<?php include 'includes/footer.php'; ?>


<script src="assets/js/script.js"></script>


</body>
</html>